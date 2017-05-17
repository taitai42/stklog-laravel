<?php

namespace taitai42\Stklog\Model;
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/10/17
 * Time: 2:46 PM
 */
class Message
{
    /**
     * @var int
     */
    public $line;

    /**
     * @var string
     */
    public $file;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var string
     */
    public $request_id;

    /**
     * @var string
     */
    public $message;

    /**
     * @var array
     */
    public $extra;

    /**
     * @var int
     */
    public $level;

    public function __construct($request_id, array $record)
    {

        $error = new \Exception();

        $this->request_id = $request_id;
        $trace = $error->getTrace();

        $trace = $trace[4]; //we always gonna have this file, stklog, and the log facade first.

        $this->file = $trace['file'];
        $this->line = $trace['line'];
        $this->timestamp = $record['datetime']->format(\DateTime::ATOM);
        $this->message = $record['message'];
        $this->extra = (object) array_merge($record['context'], $record['extra']);
        $this->level = $record['level'];
    }

}