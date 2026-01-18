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
 * ServerBag represents HTTP server and environment parameters ($_SERVER)
 * Extends ParameterBag to provide server variables and environment data
 */
final class ServerBag extends ParameterBag
{
    /**
     * Constructor
     *
     * @param array $parameters Initial parameters from $_SERVER
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /**
     * Gets an HTTP header from the server variables
     *
     * @param string $key The header key (e.g., 'Content-Type')
     * @param mixed $default The default value if the header is not found
     * @return mixed The header value
     */
    public function getHeader(string $key, mixed $default = null): mixed
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->get($key, $default);
    }
}
