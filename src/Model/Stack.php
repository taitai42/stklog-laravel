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
        $trace = $trace[1];

        $this->extra = (object)$extra;
        $this->file = $trace['file'];
        $this->line = $trace['line'];
        $this->timestamp = (new \DateTime('now'))->format(\DateTime::ATOM);
	if (isset($_SERVER['SERVER_NAME'])) {
	    $this->hostname = $_SERVER['SERVER_NAME'];
	}
    }
}
