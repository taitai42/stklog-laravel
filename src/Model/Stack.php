<?php

namespace taitai42\Stklog\Model;
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/10/17
 * Time: 2:46 PM
 */
class Stack
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
    public $name;

    /**
     * @var string
     */
    public $parent_request_id;

    /**
     * @var object
     */
    public $extra;

    public function __construct($name = null, $request_id = null, $extra = [])
    {
        $this->name = $name;

        $this->request_id = $request_id;

        if (!$request_id) {
            $this->request_id = uniqid('stklog');
        }

        $error = new \Exception();

        $trace = $error->getTrace();
        $trace = $trace[3]; //we always gonna have this file, stklog, and the log facade first.

        $this->extra = (object)$extra;
        $this->file = $trace['file'];
        $this->line = $trace['line'];
        $this->timestamp = (new \DateTime('now', new \DateTimeZone(config('app.timezone'))))->format(config('stklog.timestampformat'));
    }

}
