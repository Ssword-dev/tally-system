<?php

namespace App\Router\Traits;

use App\Router\Router;
use Closure;

trait MiddlewareHooks
{
    public function middleware(
        array $methods,
        string $pattern,
        callable $handler
    ) {
        return Router::getInstance()->registerMiddleware(
            $methods,
            $pattern,
            $handler
        );
    }
}
