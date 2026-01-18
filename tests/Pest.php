<?php

// Bootstrap test environment
require_once __DIR__ . '/bootstrap.php';

/*
|--------------------------------------------------------------------------
| Pest Configuration
|--------------------------------------------------------------------------
|
| This file is where you may configure all of the settings for Pest
| Testing Suite. Please consult the documentation for more information.
|
*/

use PHPUnit\Framework\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions are always bound to a
| specific PHPUnit test case class. By default, that is the PHPUnit\Framework\TestCase
| class; however, you may change it here.
|
*/

uses(TestCase::class)->in('tests');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're testing multiple related assertions, you often would like to do
| so without littering your test with multiple "assert" statements. You may
| use the following "expectation" methods to bundle your assertions.
|
*/

// expect()->toBeString();

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Pest allows you to define custom helper functions for your tests. Here
| you can do that.
|
*/

function makeRouter()
{
    return \App\Router\Router::getInstance();
}

function makeFluentAPI()
{
    return \App\Router\FluentAPI::getInstance();
}

function makeRoute($path, $methods = ['GET'], $handler = null)
{
    $handler = $handler ?? function () {
        return 'OK'; };
    return new \App\Router\Route($path, $methods, $handler);
}

function makeMiddlewareSequence($parent = null)
{
    return new \App\Router\MiddlewareSequence($parent);
}

function makeRequest($path = '/', $method = 'GET')
{
    return new \App\Router\Request(
        $path,
        $method,
        [],
        [],
        [],
        [],
        [],
        []
    );
}
