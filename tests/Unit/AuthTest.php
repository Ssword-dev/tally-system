<?php

namespace Tests\Unit;

use App\Auth;
use App\Exceptions\AuthException;
use Exception;

describe('Auth', function () {
    it('Auth class exists', function () {
        expect(class_exists(Auth::class))->toBeTrue();
    });

    it('AuthException exists', function () {
        expect(class_exists(AuthException::class))->toBeTrue();
    });

    it('AuthException extends Exception', function () {
        $exception = new AuthException('test message');

        expect($exception)->toBeInstanceOf(Exception::class);
        expect($exception->getMessage())->toBe('test message');
    });
});
