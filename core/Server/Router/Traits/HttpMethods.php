<?php

namespace App\Router\Traits;

use Closure;
use App\Router\Route;

trait HttpMethods
{
    public function get(string $path, Closure|string|array $handler)
    {
        return $this->register(['GET'], $path, $handler);
    }

    public function post(string $path, Closure|string|array $handler)
    {
        return $this->register(['POST'], $path, $handler);
    }

    public function put(string $path, Closure|string|array $handler)
    {
        return $this->register(['PUT'], $path, $handler);
    }

    public function patch(string $path, Closure|string|array $handler)
    {
        return $this->register(['PATCH'], $path, $handler);
    }

    public function delete(string $path, Closure|string|array $handler)
    {
        return $this->register(['DELETE'], $path, $handler);
    }
}
