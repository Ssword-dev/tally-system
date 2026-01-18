<?php
namespace App\Router;

use App\Router\RoutePattern;
use Closure;

class Layer
{
    public Closure $handler;
    public RoutePattern $pattern;
    public array $methods;

    public function __construct(
        array $methods,
        string $pattern,
        callable $handler
    ) {
        $this->pattern = RoutePattern::compile($pattern);
        $this->methods = $methods;
        $this->handler = Closure::fromCallable($handler);
    }
}