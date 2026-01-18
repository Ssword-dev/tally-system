<?php

namespace Tests\Unit;

use App\Foundation\HistoryList;
use App\Router\MiddlewareSequence;


describe('MiddlewareSequence', function () {
    it('adds middleware', function () {
        $seq = makeMiddlewareSequence();
        $seq->add('AuthMiddleware');
        $seq->add('CorsMiddleware');

        expect($seq->count())->toBe(2);
    });

    it('adds multiple middleware', function () {
        $seq = makeMiddlewareSequence();
        $seq->addMultiple('Auth', 'Cors', 'Log');

        var_dump($seq->toArray());

        expect($seq->toArray())->toBe([
            'Auth',
            'Cors',
            'Log'
        ]);
        expect($seq->count())->toBe(3);
    });

    it('converts to array', function () {
        $seq = makeMiddlewareSequence();
        $seq->add('Auth');
        $seq->add('Log');

        expect($seq->toArray())->toBe(['Auth', 'Log']);
    });

    it('iterates middleware', function () {
        $seq = makeMiddlewareSequence();
        $seq->add('Auth');
        $seq->add('Log');

        $result = [];
        foreach ($seq as $mw) {
            $result[] = $mw;
        }

        expect($result)->toBe(['Auth', 'Log']);
    });

    it('inherits from parent sequence', function () {
        $global = makeMiddlewareSequence();
        $global->add('Global1');
        $global->add('Global2');

        $group = makeMiddlewareSequence($global);
        $group->add('Group1');

        $result = [];
        foreach ($group as $mw) {
            $result[] = $mw;
        }

        expect($result)->toBe(['Global1', 'Global2', 'Group1']);
    });

    it('counts total middleware including parents', function () {
        $global = makeMiddlewareSequence();
        $global->add('G1');
        $global->add('G2');

        $group = makeMiddlewareSequence($global);
        $group->add('Gr1');

        expect($global->getTotalCount())->toBe(2);
        expect($group->getTotalCount())->toBe(3);
    });

    it('handles three-level nesting', function () {
        $global = makeMiddlewareSequence();
        $global->add('Global');

        $admin = makeMiddlewareSequence($global);
        $admin->add('AdminAuth');

        $route = makeMiddlewareSequence($admin);
        $route->add('ValidateUser');

        expect($route->toArray())->toBe(['Global', 'AdminAuth', 'ValidateUser']);
        expect($route->getTotalCount())->toBe(3);
    });
});
