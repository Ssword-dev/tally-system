<?php

namespace Core\Server\Foundation;

use Iterator;

/**
 * A doubly-linked list implementation using LinkedNode
 * Implements Iterator for easy traversal
 */

// TODO: Implement IteratorAggregate instead.
class LinkedList implements Iterator
{
    /**
     * The head node of the list
     */
    private ?LinkedNode $head = null;

    /**
     * The tail node of the list
     */
    private ?LinkedNode $tail = null;

    /**
     * The number of items in the list
     */
    private int $count = 0;

    /**
     * Current node for iteration
     */
    private ?LinkedNode $current = null;

    /**
     * Current position for iteration
     */
    private int $position = 0;

    /**
     * append a value to the end of the list
     */
    public function append(...$values): void
    {
        foreach ($values as $value) {
            $node = new LinkedNode($value);

            if ($this->tail === null) {
                // First node
                $this->head = $node;
                $this->tail = $node;
            } else {
                // Core\Serverend to tail
                $this->tail->linkNext($node);
                $this->tail = $node;
            }

            $this->count++;
        }
    }

    /**
     * Prepend a value to the start of the list
     */
    public function prepend(mixed $value): void
    {
        $node = new LinkedNode($value);

        if ($this->head === null) {
            // First node
            $this->head = $node;
            $this->tail = $node;
        } else {
            // Prepend to head
            $node->linkNext($this->head);
            $this->head = $node;
        }

        $this->count++;
    }

    /**
     * Check if list is empty
     */
    public function isEmpty(): bool
    {
        return $this->count === 0;
    }

    /**
     * Get the number of items in the list
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Get the head node
     */
    public function getHead(): ?LinkedNode
    {
        return $this->head;
    }

    /**
     * Get the tail node
     */
    public function getTail(): ?LinkedNode
    {
        return $this->tail;
    }

    /**
     * Iterator: Get the current value
     */
    public function current(): mixed
    {
        return $this->current?->getValue();
    }

    /**
     * Iterator: Get the current key (position)
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Iterator: Move to the next item
     */
    public function next(): void
    {
        if ($this->current !== null) {
            $this->current = $this->current->next;
            $this->position++;
        }
    }

    /**
     * Iterator: Reset to the beginning
     */
    public function rewind(): void
    {
        $this->current = $this->head;
        $this->position = 0;
    }

    /**
     * Iterator: Check if current is valid
     */
    public function valid(): bool
    {
        return $this->current !== null;
    }

    /**
     * Get all items as an array
     */
    public function toArray(): array
    {
        $items = [];
        $node = $this->head;

        while ($node !== null) {
            $items[] = $node->getValue();
            $node = $node->next;
        }

        return $items;
    }

    /**
     * Reverse iteration (from tail to head)
     */
    public function reverseIterator(): Iterator
    {
        return new ReverseLinkedListIterator($this->tail);
    }
}

/**
 * Reverse iterator for linked list
 */
class ReverseLinkedListIterator implements Iterator
{
    private ?LinkedNode $current;
    private int $position = 0;

    public function __construct(?LinkedNode $tail)
    {
        $this->current = $tail;
    }

    public function current(): mixed
    {
        return $this->current?->getValue();
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        if ($this->current !== null) {
            $this->current = $this->current->prev;
            $this->position++;
        }
    }

    public function rewind(): void
    {
        // Reverse iteration always starts from tail (current position)
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }
}
