<?php

namespace App\Router;

use App\Traits\Singleton;
use App\Router\Traits\HttpMethods;
use App\Router\Traits\RouteRegistration;
use App\Router\Traits\Groups;
use App\Router\Traits\MiddlewareHooks;
use App\Router\Traits\StaticServing;

final class FluentAPI
{
    use Singleton;
    use HttpMethods;
    use RouteRegistration;
    use Groups;
    use MiddlewareHooks;
    use StaticServing;

    /**
     * The underlying router instance
     *
     * @var Router
     */
    private Router $router;

    /**
     * Current group prefix
     *
     * @var string
     */
    private string $groupPrefix = '';

    /**
     * Current group middleware
     *
     * @var array
     */
    private array $groupMiddleware = [];

    /**
     * Constructor
     *
     * @param FluentAPI $instance
     */
    public static function initInstance(FluentAPI $instance)
    {
        $instance->router = Router::getInstance();
    }

    /**
     * Get the underlying router instance
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}
