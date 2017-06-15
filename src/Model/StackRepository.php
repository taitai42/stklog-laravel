<?php
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/17/17
 * Time: 11:24 AM
 */

namespace taitai42\Stklog\Model;


use taitai42\Stklog\Handler\StklogTcpHandler;

class StackRepository
{
    /**
     * @var StackRepository
     */
    protected static $instance = null;

    /**
     * @var []
     */
    public $last_stack;

    /**
     * @var array of Stack
     */
    public $stacks;

    /**
     * this class should be a singleton ...
     * @return null|StackRepository
     */
    public static function getInstance($driver = null) {
        if (!self::$instance) {
            self::$instance = new StackRepository($driver);
        }

        return self::$instance;
    }


    /**
     * StackRepository constructor.
     */
    protected function __construct($driver = null)
    {
        $this->stacks = [];
        $this->last_stack = null;
        $this->driver = $driver;
    }

    /**
     * create a new stack
     *
     * @param null  $name name of the stack
     * @param null  $request_id request_id of the stack
     * @param array $context context to pass on teh stack
     */
    public static function stack($name = null, $request_id = null, array $context = [])
    {
        $rep = self::getInstance();

        $stack = new Stack($name, $request_id, $context);
        $stack->parent_request_id = $rep->end($rep->last_stack);
        $rep->last_stack[] = $stack->request_id;
        $rep->stacks[] = $stack;
        if ($rep->driver instanceof StklogTcpHandler) {
            $rep->sendStack($stack);
        }
    }

    public function sendStack($stack) {
        $this->driver->send($stack);
    }
    /**
     * close the last stack
     * @param null $name
     */
    public static function endstack($name = null, $request_id = null)
    {
        $rep = self::getInstance();

        $key = array_search($request_id, $rep->last_stack);
        unset($rep->last_stack[$key]);
    }

    public function getStacks() {
        return $this->stacks;
    }

    /**
     * Return last id of the last stack, or create one if no one exists
     * @return mixed|null
     */
    public function getLastId()
    {
        $id = $this->end($this->last_stack);
        if ($id === null) {
            $this->stack('default');

            return $this->getLastId();
        }

        return $id;

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

}