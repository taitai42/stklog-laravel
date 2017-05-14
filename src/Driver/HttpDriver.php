<?php
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/10/17
 * Time: 11:41 AM
 */

namespace taitai42\Stklog\Driver;


use Ixudra\Curl\Facades\Curl;
use taitai42\Stklog\Contracts\Driver;
use taitai42\Stklog\Model\Message;
use taitai42\Stklog\Model\Stack;

/**
 * Class HttpDriver
 *
 * @package taitai42\Stklog\Driver
 */
class HttpDriver implements Driver
{
    /**
     * Api url
     */
    const API_URL = "https://api.stklog.io";

    /**
     * Stacks endpoint
     */
    const STACKS_ENDPOINT = "/stacks";

    /**
     * Logs endpoint
     */
    const LOGS_ENDPOINT = "/logs";

    const MAX_LOG_BUFFER = 5;

    /**
     * stklog project key.
     *
     * @var string
     */
    private $key;

    private $max_log_buffer;

    /**
     * Log severity level.
     *
     * @var int
     */
    private $severity = LOG_DEBUG;

    /**
     * Class singleton.
     *
     * @var static
     */
    private static $instance;

    /**
     * @var Stack[]
     */
    protected $stacks;

    /**
     * @var Message[]
     */
    protected $logs;

    /**
     * @var array
     */
    protected $last_stack;

    /**
     * Driver constructor.
     *
     * @param  string $token
     * @param  bool   $persistent
     * @param  bool   $useSsl
     * @param  iont   $severity
     */
    public function __construct($config, $severity)
    {
        $this->token = $config['project_key'];

        $this->severity = $severity;

        $this->max_log_buffer = isset($config['max_log_buffer']) ? $config['max_log_buffer'] : self::MAX_LOG_BUFFER;
    }

    /**
     * Driver destructor.
     *
     * When the driver is destructed, which should happen at the end of the request, we should send
     * what's left to stklog.
     */
    public function __destruct()
    {
        $this->send();
    }

    /**
     * Log given line with given severity.
     *
     * @param  string $line
     * @param  int    $logSeverity
     *
     * @return void
     */
    public function log($line, $logSeverity, $context)
    {
        $message = new Message($this->getLastId(), $line, $logSeverity, $context);
        $this->logs[] = $message;
        if (count($this->logs) > $this->max_log_buffer) {
            $this->send();
        }
    }

    /**
     * @param null $name
     */
    public function stack($name = null, $request_id = null, array $context = [])
    {
        $stack = new Stack($name, $request_id, $context);
        $stack->parent_request_id = $this->end($this->last_stack);
        $this->last_stack[] = $stack->request_id;
        $this->stacks[] = $stack;
    }

    /**
     * @param null $name
     */
    public function endstack($name = null, $request_id = null)
    {

        $key = array_search($request_id, $this->last_stack);
        unset($this->last_stack[$key]);
    }

    /**
     * Quick helper to get the last element of an array without
     * having to move the internal pointer
     *
     * @param $array
     *
     * @return mixed|null
     */
    public function end($array)
    {
        return $array ? end($array) : null;
    }

    /**
     * When we send the data to the api
     * We should always send the stack first, then the logs.
     */
    public function send()
    {
        //TODO: handle post error\
        $this->post(self::STACKS_ENDPOINT, json_encode($this->stacks));
        $this->post(self::LOGS_ENDPOINT, json_encode($this->logs));
        $this->logs = [];
    }

    /**
     * @param $endpoint
     * @param $data
     *
     * @return array
     */
    public function post($endpoint, $data)
    {
        $ch = curl_init(self::API_URL . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'X-Stklog-Project-Key: ' . $this->token,
            ]
        );

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        return [$result, $errno];
    }

    /**
     * @return mixed|null
     */
    public function getLastId()
    {
        $id = $this->end($this->last_stack);
        if ($id === null) {
            $this->stack();

            return $this->getLastId();
        }

        return $id;

    }
}
