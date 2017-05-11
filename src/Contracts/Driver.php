<?php

namespace taitai42\Stklog\Contracts;


/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/10/17
 * Time: 2:12 PM
 */
interface Driver
{
    public function log($line, $logSeverity, $context);

    public function stack($name = null, $request_id = null, array $context = []);

    public function endstack($name = null, $request_id = null);

    public function send();
}