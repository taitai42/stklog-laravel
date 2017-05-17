# stklog-laravel

This package is a stklog wrapper to send your log to stklog.
please check stklog.io for more information on how to get your project_key


## Install

Via Composer

``` bash
$ composer require taitai42/stklog-laravel
```

add the service provider to your `config/app.php` file

```php
'providers' => [
    ...,
    \taitai42\Stklog\StklogServiceProvider::class,
  ],

```
publish the config file : 

```bash
php artisan vendor:publish

```

you can optionnally use the provided stklog middleware that will log all of your request to your app.
to do so add this line in your kernel.php file :

```php
protected $middleware = [
 ...,
 
 taitai42\Stklog\Middleware\StklogMiddleware::class,
];
```

## Usage

stklog-laravel package will automatically overwrite the log interface of your laravel app,
meaning you can simply use your normal logging way and everything
will be forwarded to stklog 

you can also declare your own stack by using the stack repository :
```php
// use taitai42\Stklog\Model\StackRepository;

StackRepository::stack('incoming request', null, $request->headers->all());
Log::info("parsing request ...");
 
```

You can also use this handler outside laravel, to do so simply add the new handler with the transport of your choice to your monolog instance :

```php
<?php

use Monolog\Logger;
use taitai42\Stklog\Handler\StklogHttpHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StklogHttpHandler('your-project-key', Logger::WARNING));

// add records to the log
$log->warning('Foo');
$log->error('Bar');
```

## Todo

- set the default log level in config
- test nesting stack when it will be working
- endstack by name (check nested stack)
- add test ? (or be confident.)
