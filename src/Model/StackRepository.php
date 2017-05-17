<?php
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/17/17
 * Time: 11:24 AM
 */

namespace taitai42\Stklog\Model;


class StackRepository
{
    protected static $instance = null;

    public $last_stack;

    public $stacks;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new StackRepository();
        }

        return self::$instance;
    }


    protected function __construct()
    {
        $this->stacks = [];
        $this->last_stack = null;
    }

    /**
     * @param null $name
     */
    public static function stack($name = null, $request_id = null, array $context = [])
    {
        $rep = self::getInstance();

        $stack = new Stack($name, $request_id, $context);
        $stack->parent_request_id = $rep->end($rep->last_stack);
        $rep->last_stack[] = $stack->request_id;
        $rep->stacks[] = $stack;
    }

    /**
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