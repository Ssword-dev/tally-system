<?php

namespace App\Router\Traits;

use Closure;
use App\Router\Route;

trait RouteRegistration
{
    public function register(array $methods, string $path, Closure|string|array $handler)
    {
        $fullPath = $this->groupPrefix . $path;

        $this->router->registerRoute(
            $methods,
            $fullPath,
            $handler,
        );
    }
}
