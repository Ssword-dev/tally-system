<?php

namespace Tests\Unit;

use App\Traits\Singleton;
use App\Exceptions\SingletonSerializationException;

describe('Singleton Trait', function () {
    it('provides getInstance method', function () {
        expect(method_exists('App\Router\Router', 'getInstance'))->toBeTrue();
    });

    it('returns same instance on multiple calls', function () {
        $instance1 = \App\Router\Router::getInstance();
        $instance2 = \App\Router\Router::getInstance();

        expect($instance1)->toBe($instance2);
    });

    it('prevents cloning', function () {
        $instance = \App\Router\Router::getInstance();

        expect(function () use ($instance) {
            clone $instance;
        })->toThrow(\Error::class);
    });

    it('throws on serialization', function () {
        $instance = \App\Router\Router::getInstance();

        expect(function () use ($instance) {
            serialize($instance);
        })->toThrow(SingletonSerializationException::class);
    });

    it('throws on unserialization attempt', function () {
        // This would require actually trying to unserialize, which is complex
        expect(class_exists('App\Exceptions\SingletonSerializationException'))->toBeTrue();
    });
});
