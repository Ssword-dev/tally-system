<?php

namespace Tests\Unit;

use App\Router\Route;

describe('Route', function () {
    it('creates route with path and methods', function () {
        $route = makeRoute('/users', ['GET', 'POST']);

        expect($route->getPath())->toBe('/users');
    });

    it('sets route name', function () {
        $route = makeRoute('/users')
            ->name('users.list');

        expect($route->getName())->toBe('users.list');
    });

    it('middleware methods exist and work', function () {
        $seq = makeMiddlewareSequence();
        $route = makeRoute('/admin');
        $route->setMiddlewareSequence($seq);

        $retrieved = $route->getMiddlewareSequence();
        expect($retrieved)->toBe($seq);
    });

    it('adds constraints', function () {
        $route = makeRoute('/users/{id}')
            ->where('id', '\d+');

        expect($route)->toBeInstanceOf(Route::class);
    });

    it('sets middleware sequence', function () {
        $route = makeRoute('/test');
        $seq = makeMiddlewareSequence();
        $seq->add('TestMiddleware');

        $route->setMiddlewareSequence($seq);

        expect($route->getMiddlewareSequence())->toBe($seq);
    });

    it('returns middleware as array', function () {
        $global = makeMiddlewareSequence();
        $global->add('Global');

        $seq = makeMiddlewareSequence($global);
        $seq->add('Route');

        $route = makeRoute('/test');
        $route->setMiddlewareSequence($seq);

        $mw = $route->getMiddleware();
        expect($mw)->toBe(['Global', 'Route']);
    });

    it('preserves fluent API with middleware sequence', function () {
        $seq = makeMiddlewareSequence();
        $route = makeRoute('/test');
        $route->setMiddlewareSequence($seq);

        $result = $route
            ->name('test')
            ->middleware('auth')
            ->middleware('log');

        expect($result)->toBe($route);
    });
});
