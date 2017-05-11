<?php
/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/10/17
 * Time: 11:40 AM
 */

namespace taitai42\Stklog;


use Psr\Log\LoggerInterface;
use taitai42\Stklog\Contracts\Driver;
use taitai42\Stklog\Model\Stack;

class Stklog implements LoggerInterface
{
    /**
     * Stklog logger driver.
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Stklog constructor.
     *
     * @param  Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $stackname = uniqid('stklog');
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $this->driver->log($message, $level, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = [])
    {
        $this->driver->log($message, LOG_ALERT, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = [])
    {
        $this->driver->log($message, LOG_EMERG, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = [])
    {
        $this->driver->log($message, LOG_CRIT, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = [])
    {
        $this->driver->log($message, LOG_ERR, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = [])
    {
        $this->driver->log($message, LOG_WARNING, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = [])
    {
        $this->driver->log($message, LOG_NOTICE, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = [])
    {
        $this->driver->log($message, LOG_INFO, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = [])
    {
        $this->driver->log($message, LOG_DEBUG, $context);
    }

    public function stack($name = null, $request_id = null, array $context = [])
    {
        $this->driver->stack($name, $request_id, $context);
    }

    public function endstack($name = null)
    {
        $this->driver->endstack();
    }
}