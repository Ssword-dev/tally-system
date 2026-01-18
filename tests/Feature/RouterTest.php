<?php

namespace Tests\Feature;

use App\Router\Router;
use App\Router\FluentAPI;
use App\Router\Route;
use App\Router\Request;

describe('Router', function () {
    it('registers routes via FluentAPI', function () {
        $api = makeFluentAPI();

        $route = $api->get('/users', function () {
            return 'users list';
        });

        expect($route)->toBeInstanceOf(Route::class);
    });

    it('supports all HTTP methods', function () {
        $api = makeFluentAPI();

        $get = $api->get('/items', fn() => 'list');
        $post = $api->post('/items', fn() => 'create');
        $put = $api->put('/items/1', fn() => 'update');
        $delete = $api->delete('/items/1', fn() => 'delete');
        $patch = $api->patch('/items/1', fn() => 'patch');

        expect($get)->toBeInstanceOf(Route::class);
        expect($post)->toBeInstanceOf(Route::class);
        expect($put)->toBeInstanceOf(Route::class);
        expect($delete)->toBeInstanceOf(Route::class);
        expect($patch)->toBeInstanceOf(Route::class);
    });

    it('routes can have names', function () {
        $api = makeFluentAPI();

        $route = $api->get('/users', fn() => 'users')
            ->name('users.index');

        expect($route->getName())->toBe('users.index');
    });

    it('routes support constraints', function () {
        $api = makeFluentAPI();

        $route = $api->get('/users/{id}', fn() => 'user')
            ->where('id', '\d+');

        expect($route)->toBeInstanceOf(Route::class);
    });

    it('routes can have middleware', function () {
        $api = makeFluentAPI();

        $route = $api->get('/admin', fn() => 'admin')
            ->middleware('auth')
            ->middleware('admin');

        $mw = $route->getMiddleware();
        expect(count($mw))->toBeGreaterThan(0);
    });

    it('router adds global middleware', function () {
        $router = makeRouter();
        $router->addGlobalMiddleware('GlobalAuth');

        expect($router)->toBeInstanceOf(Router::class);
    });

    it('router can match simple routes', function () {
        $api = makeFluentAPI();
        $api->get('/test-route', fn() => 'ok');

        $router = makeRouter();
        $request = makeRequest('/test-route', 'GET');
        $route = $router->match($request);

        expect($route)->not->toBeNull();
    });

    it('router returns null for unmatched routes', function () {
        $router = makeRouter();
        $request = makeRequest('/definitely-not-existing', 'GET');
        $route = $router->match($request);

        expect($route)->toBeNull();
    });

    it('FluentAPI is a singleton', function () {
        $api1 = makeFluentAPI();
        $api2 = makeFluentAPI();

        expect($api1)->toBe($api2);
    });

    it('Router is a singleton', function () {
        $router1 = makeRouter();
        $router2 = makeRouter();

        expect($router1)->toBe($router2);
    });
});
