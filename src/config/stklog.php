<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Project Key
    |--------------------------------------------------------------------------
    |
    | contains the project key of stklog where all the log should be forwarded
    |
    */
    'project_key' => env('STKLOG_PROJECT_KEY', ""),

    /*
    |--------------------------------------------------------------------------
    | Transport
    |--------------------------------------------------------------------------
    |
    | Represent the transport method to send the logs to stklog
    | Currently, stklog only support http, which means we stack every log until
    | the end of the request, in order to only make one big request.
    |
    */
    'transport' => 'http',

    /*
    |--------------------------------------------------------------------------
    | Log level
    |--------------------------------------------------------------------------
    |
    | Represent the level of log this handler should be triggered,
    | ex: if you set it to WARNING, every log with a level below WARNING (debug, info) won't
    | be sent to stklog.
    |
    */
    'level' => \Monolog\Logger::INFO,
];