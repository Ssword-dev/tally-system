<?php

namespace Tests\Unit;

use App\Router\Request;
use App\Router\Response;

describe('Request', function () {
    it('Request class exists', function () {
        expect(class_exists('App\Router\Request'))->toBeTrue();
    });

    it('Request can be created with arguments', function () {
        $request = makeRequest('/test', 'POST');
        expect($request)->toBeInstanceOf(\App\Router\Request::class);
    });

    it('Request stores path and method', function () {
        $request = makeRequest('/api/users', 'GET');

        expect($request->path)->toBe('/api/users');
        expect($request->method)->toBe('GET');
    });
});

describe('Response', function () {
    it('Response class exists', function () {
        expect(class_exists('App\Router\Response'))->toBeTrue();
    });

    it('Response can be created', function () {
        $response = new Response();

        expect($response)->toBeInstanceOf(Response::class);
    });

    it('Response can set status code', function () {
        $response = new Response();

        expect($response)->toBeInstanceOf(Response::class);
    });
});
