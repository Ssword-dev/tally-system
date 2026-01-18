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
 * 
 * HeaderBag represents HTTP headers
 * Extends ParameterBag and provides case-insensitive header access
 */
final class HeaderBag extends ParameterBag
{
    /**
     * Constructor
     *
     * @param array $parameters Initial parameters with header data
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($this->normalizeKeys($parameters));
    }

    /**
     * Normalizes header keys to lowercase for case-insensitive access
     *
     * @param array $parameters The header parameters
     * @return array The normalized parameters
     */
    private function normalizeKeys(array $parameters): array
    {
        $normalized = [];
        foreach ($parameters as $key => $value) {
            $normalized[strtolower($key)] = $value;
        }
        return $normalized;
    }

    /**
     * Returns a parameter value by name (case-insensitive)
     *
     * @param string $key The parameter key
     * @param mixed $default The default value if the parameter key does not exist
     * @return mixed The parameter value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return parent::get(strtolower($key), $default);
    }

    /**
     * Returns true if the parameter is defined (case-insensitive)
     *
     * @param string $key The parameter key
     * @return bool true if the parameter exists, false otherwise
     */
    public function has(string $key): bool
    {
        return parent::has(strtolower($key));
    }

    /**
     * Sets a parameter by name (case-insensitive)
     *
     * @param string $key The parameter key
     * @param mixed $value The parameter value
     */
    public function set(string $key, mixed $value): void
    {
        parent::set(strtolower($key), $value);
    }

    /**
     * Removes a parameter (case-insensitive)
     *
     * @param string $key The parameter key
     */
    public function remove(string $key): void
    {
        parent::remove(strtolower($key));
    }
}
