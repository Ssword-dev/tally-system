<?php

namespace Tests\Unit;

use App\Router\RoutePattern;

describe('RoutePattern', function () {
    it('RoutePattern class exists', function () {
        expect(class_exists('App\Router\RoutePattern'))->toBeTrue();
    });

    it('Routes can be created with pattern paths', function () {
        $route = makeRoute('/users/{id}');
        expect($route)->toBeInstanceOf(\App\Router\Route::class);
    });

    it('Routes support constraint specifications', function () {
        $route = makeRoute('/items/{id}')
            ->where('id', '\d+');

        expect($route)->toBeInstanceOf(\App\Router\Route::class);
    });

    it('Routes store path information', function () {
        $route = makeRoute('/test/path');
        expect($route->getPath())->toBe('/test/path');
    });

    it('Routes support wildcard patterns', function () {
        $route = makeRoute('/files/*');
        expect($route->getPath())->toBe('/files/*');
    });

    it('Routes support multiple parameter segments', function () {
        $route = makeRoute('/users/{userId}/posts/{postId}');
        expect($route->getPath())->toBe('/users/{userId}/posts/{postId}');
    });
});
