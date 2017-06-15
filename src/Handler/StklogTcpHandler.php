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
use MongoDB\Driver\Manager;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\SocketHandler;
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
class StklogTcpHandler extends SocketHandler
{
    /**
     * Api url
     */
    const SOCKET_URL = "tcp://api.stklog.io:4242";

    /**
     * Stacks endpoint
     */
    const STACK_TYPE = "stack";

    /**
     * Logs endpoint
     */
    const LOG_TYPE = "log";


    /**
     * @var StackRepository
     */
    protected $stacks;


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
        parent::__construct(self::SOCKET_URL, $level, $bubble);

        $this->token = $project_key;

        $this->stacks = StackRepository::getInstance($this);
    }



    /**
     * When we send the data to the api
     * We should always send the stack first, then the logs.
     */
    public function send($data)
    {
        $type = self::STACK_TYPE;
        if ($data instanceof Message) {
            $type = self::LOG_TYPE;
        }

        $replace = [
            '{project_key}' => $this->token,
            '{type}' => $type,
            '{message}' => str_replace('\n', '', json_encode($data)),
        ];

        $message = implode(chr(9), $replace) . "\n";
        parent::write(['formatted' => $message]);
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
        $this->send($message);
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
