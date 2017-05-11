# :stklog-laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package is a stklog wrapper to send your log to stklog.
please check stklog.io for more information on how to get your project_key


## Install

Via Composer

``` bash
$ composer require taitai42/stklog-laravel
```

add the service provider to your `config/app.php` file

```php

\taitai42\Stklog\StklogServiceProvider::class,

```
publish the config file : 

```bash
php artisan vendor:publish

```

## Usage

stklog-laravel package will automatically overwrite the log interface of your laravel app,
meaning you can simply use your normal logging way and everything
will be forwarded to stklog

## Todo

- stop using laravel function to be able to use this lib outside of laravel.
- test nesting stack when it will be working
- endstack by name (check nested stack)
- add test ? (or be confident.)