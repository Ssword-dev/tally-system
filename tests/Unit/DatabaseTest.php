<?php

namespace Tests\Unit;

use App\Database;
use App\Traits\Singleton;

describe('Database', function () {
    it('Database class exists', function () {
        expect(class_exists(Database::class))->toBeTrue();
    });

    it('Database is a singleton', function () {
        // Get class and check for Singleton trait
        $reflection = new \ReflectionClass(Database::class);
        $traits = $reflection->getTraitNames();

        expect($traits)->toContain(Singleton::class);
    });
});
