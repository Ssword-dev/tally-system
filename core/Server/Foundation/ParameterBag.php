<?php

namespace Core\Server\Foundation;

/**
 * This class is adapted from Symfony HTTP Foundation:
 * https://github.com/symfony/http-foundation
 *
 * Original authors:
 *  - Fabien Potencier and contributors
 *
 * MIT License
 */

/**
 * ParameterBag is a data structure that holds key-value pairs
 * Provides a convenient API to access, manipulate, and iterate over parameters
 */
class ParameterBag
{
    /**
     * @var array The parameters stored in this bag
     */
    protected array $parameters = [];

    /**
     * Constructor
     *
     * @param array $parameters Initial parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns a parameter value by name
     *
     * @param string $key The parameter key
     * @param mixed $default The default value if the parameter key does not exist
     * @return mixed The parameter value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * Sets a parameter by name
     *
     * @param string $key The parameter key
     * @param mixed $value The parameter value
     */
    public function set(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined
     *
     * @param string $key The parameter key
     * @return bool true if the parameter exists, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->parameters[$key]);
    }

    /**
     * Removes a parameter
     *
     * @param string $key The parameter key
     */
    public function remove(string $key): void
    {
        unset($this->parameters[$key]);
    }

    /**
     * Returns all parameters as an array
     *
     * @return array All parameters
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the number of parameters
     *
     * @return int The number of parameters
     */
    public function count(): int
    {
        return count($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set
     *
     * @param array $parameters An array of parameters
     */
    public function replace(array $parameters = []): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameters to the existing set
     *
     * @param array $parameters An array of parameters
     */
    public function add(array $parameters = []): void
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    /**
     * Returns an iterator for parameters
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * Returns an array representation of the parameters
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->parameters;
    }
}
