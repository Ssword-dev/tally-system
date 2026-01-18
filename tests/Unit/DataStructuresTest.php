<?php

namespace Tests\Unit;

use App\Foundation\LinkedNode;
use App\Foundation\LinkedList;

describe('LinkedNode', function () {
    it('stores and retrieves values', function () {
        $node = new LinkedNode('test-value');
        expect($node->getValue())->toBe('test-value');
    });

    it('tracks next and prev pointers', function () {
        $node1 = new LinkedNode('first');
        $node2 = new LinkedNode('second');

        $node1->linkNext($node2);

        expect($node1->hasNext())->toBeTrue();
        expect($node2->hasPrev())->toBeTrue();
    });

    it('can store any type of value', function () {
        $intNode = new LinkedNode(42);
        $arrNode = new LinkedNode(['key' => 'value']);
        $objNode = new LinkedNode((object) ['prop' => 'val']);

        expect($intNode->getValue())->toBe(42);
        expect($arrNode->getValue())->toBe(['key' => 'value']);
        expect($objNode->getValue())->toHaveProperty('prop');
    });
});

describe('LinkedList', function () {
    it('appends items correctly', function () {
        $list = new LinkedList();
        $list->append('first');
        $list->append('second');

        expect($list->count())->toBe(2);
        expect($list->toArray())->toBe(['first', 'second']);
    });

    it('prepends items correctly', function () {
        $list = new LinkedList();
        $list->append('second');
        $list->prepend('first');

        expect($list->toArray())->toBe(['first', 'second']);
    });

    it('detects empty list', function () {
        $list = new LinkedList();
        expect($list->isEmpty())->toBeTrue();

        $list->append('item');
        expect($list->isEmpty())->toBeFalse();
    });

    it('iterates in order', function () {
        $list = new LinkedList();
        $list->append('a');
        $list->append('b');
        $list->append('c');

        $result = [];
        foreach ($list as $item) {
            $result[] = $item;
        }

        expect($result)->toBe(['a', 'b', 'c']);
    });

    it('has head and tail', function () {
        $list = new LinkedList();
        $list->append('first');
        $list->append('middle');
        $list->append('last');

        expect($list->getHead()->getValue())->toBe('first');
        expect($list->getTail()->getValue())->toBe('last');
    });

    it('converts to array', function () {
        $list = new LinkedList();
        $list->append(1);
        $list->append(2);
        $list->append(3);

        expect($list->toArray())->toBe([1, 2, 3]);
    });
});
