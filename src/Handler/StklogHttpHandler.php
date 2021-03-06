<?php
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/10/17
 * Time: 11:41 AM
 */

namespace taitai42\Stklog\Handler;


use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use taitai42\Stklog\Contracts\Driver;
use taitai42\Stklog\Model\Message;
use taitai42\Stklog\Model\Stack;
use taitai42\Stklog\Model\StackRepository;

/**
 * Class HttpDriver
 *
 * @package taitai42\Stklog\Driver
 */
class StklogHttpHandler extends AbstractProcessingHandler
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


    /**
     * @var StackRepository
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
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @var string project key
     */
    private $token;

    /**
     * Driver constructor.
     *
     * @param  string  $project_key your stklog project key
     * @param bool|int $level level to start triggering this handler
     * @param bool     $bubble
     */
    public function __construct($project_key, $level = Logger::INFO, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->token = $project_key;

        $this->stacks = StackRepository::getInstance($this);
    }



    /**
     * When we send the data to the api
     * We should always send the stack first, then the logs.
     */
    public function send()
    {
        //TODO: handle post error\
        $this->post(self::STACKS_ENDPOINT, json_encode($this->stacks->getStacks()));
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
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        $message = new Message($this->stacks->getLastId(), $record);
        $this->logs[] = $message;
    }

    /**
     * this method will be called when the logger will be destructed
     */
    public function close()
    {
        parent::close();

        $this->send();
    }
}
