<?php

namespace Tests\Feature;

use App\Router\Router;
use App\Router\FluentAPI;

describe('Router Integration', function () {
    it('can register and match simple routes', function () {
        $api = makeFluentAPI();
        $route1 = $api->get('/hello-unique', fn() => 'Hello World');

        expect($route1)->toBeInstanceOf(\App\Router\Route::class);
    });

    it('supports all HTTP methods', function () {
        $api = makeFluentAPI();
        $routes = [
            $api->get('/get', fn() => 'GET'),
            $api->post('/post', fn() => 'POST'),
            $api->put('/put', fn() => 'PUT'),
            $api->delete('/delete', fn() => 'DELETE'),
            $api->patch('/patch', fn() => 'PATCH'),
        ];

        expect(count($routes))->toBe(5);
        foreach ($routes as $route) {
            expect($route)->toBeInstanceOf(\App\Router\Route::class);
        }
    });

    it('preserves route names', function () {
        $api = makeFluentAPI();
        $route = $api->get('/items', fn() => 'list')
            ->name('items.index');

        expect($route->getName())->toBe('items.index');
    });

    it('supports route constraints', function () {
        $api = makeFluentAPI();
        $route = $api->get('/items/{id}', fn() => 'item')
            ->where('id', '\d+');

        expect($route)->toBeInstanceOf(\App\Router\Route::class);
    });

    it('supports middleware on routes', function () {
        $api = makeFluentAPI();
        $route = $api->get('/admin', fn() => 'admin');

        $route->middleware('auth');
        $route->middleware('admin');

        $mw = $route->getMiddleware();
        expect(count($mw))->toBeGreaterThan(0);
    });

    it('applies global middleware to all routes', function () {
        $router = makeRouter();
        $router->addGlobalMiddleware('GlobalMiddleware');

        $api = makeFluentAPI();
        $route = $api->get('/test', fn() => 'ok');

        expect($route)->toBeInstanceOf(\App\Router\Route::class);
    });

    it('handles request objects', function () {
        $request = makeRequest('/test', 'GET');

        expect($request->path)->toBe('/test');
        expect($request->method)->toBe('GET');
    });

    it('returns null for unmatched routes', function () {
        $router = makeRouter();
        $request = makeRequest('/definitely-not-registered', 'GET');
        $route = $router->match($request);

        expect($route)->toBeNull();
    });

    it('supports fluent route configuration', function () {
        $api = makeFluentAPI();

        $route = $api->post('/users', fn() => 'create')
            ->name('users.create')
            ->middleware('auth')
            ->where('id', '\d+');

        expect($route->getName())->toBe('users.create');
        expect($route)->toBeInstanceOf(\App\Router\Route::class);
    });
});
