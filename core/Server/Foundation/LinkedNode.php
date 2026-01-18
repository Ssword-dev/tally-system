<?php

namespace Core\Server\Foundation;

/**
 * Basic linked list node that can be part of a chain
 * Each node holds a value and can point to next and/or previous nodes
 */
final class LinkedNode
{
    /**
     * The value stored in this node
     */
    public mixed $value;

    /**
     * The next node in the chain
     */
    public ?LinkedNode $next = null;

    /**
     * The previous node in the chain (for doubly-linked lists)
     */
    public ?LinkedNode $prev = null;

    /**
     * Create a new linked node with a value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Link this node to the next node
     */
    public function linkNext(LinkedNode $node): void
    {
        $this->next = $node;
        $node->prev = $this;
    }

    /**
     * Get the value stored in this node
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Check if this node has a next node
     */
    public function hasNext(): bool
    {
        return $this->next !== null;
    }

    /**
     * Check if this node has a previous node
     */
    public function hasPrev(): bool
    {
        return $this->prev !== null;
    }
}
