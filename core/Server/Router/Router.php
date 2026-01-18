<?php

namespace App\Router;

use App\Foundation\ParameterBag;
use App\Traits\Singleton;
use Closure;
use RuntimeException;

final class Router
{
    use Singleton;

    /**
     * @var Layer[]
     */
    private array $layers;

    private array $hooks = [];

    private ?Closure $notFoundHandler;
    protected static function initInstance(Router $instance)
    {
        $instance->layers = [];
        $instance->hooks = [
            'beforeHandle' => [],
            'afterHandle' => [],
            'notFound' => [],
        ];
        $instance->notFoundHandler = null;
    }

    /**
     * register a route with methods, path pattern, handler, and optional route middleware
     */
    public function registerRoute(
        array $methods,
        string $pattern,
        callable $handler
    ) {
        $layer = new Layer($methods, $pattern, $handler);
        $this->layers[] = $layer;
    }

    /**
     * add a global middleware
     */
    public function registerMiddleware(
        array $methods,
        string $pattern,
        callable $handler
    ): void {
        $layer = new Layer($methods, $pattern, $handler);
        $this->layers[] = $layer;
    }

    /**
     * register hook listener
     */
    public function registerHook(string $event, Closure $callback): void
    {
        if (isset($this->hooks[$event])) {
            $this->hooks[$event][] = $callback;
        }
    }

    private function emit(string $event, ...$args): void
    {
        foreach ($this->hooks[$event] ?? [] as $hook) {
            $hook(...$args);
        }
    }

    /**
     * handle request
     */
    public function handle(Request $request): Response
    {
        $this->emit('beforeHandle', $request);

        $response = null;

        foreach ($this->layers as $layer) {
            $match = $layer->pattern->matches($request->path);

            if ($match === false)
                continue;

            $request->routeParams->replace($match);
            $response = ($layer->handler)($request, $response) ?? $response;

            // thats a pretty wild syntax. in js, we just have
            // `?.`
            if ($response?->isFinalResponse()) {
                break;
            }
        }

        if ($response === null && $this->notFoundHandler !== null) {
            return ($this->notFoundHandler)($request);
        }

        if ($response === null) {
            return Response::notFound('Not Found');
        }

        return $response;
    }
}
