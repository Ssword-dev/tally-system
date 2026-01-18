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
 * InputBag represents HTTP request input parameters (GET, POST, COOKIE data)
 * Extends ParameterBag to provide user input data
 */
final class InputBag extends ParameterBag
{
    /**
     * Constructor
     *
     * @param array $parameters Initial parameters from user input
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }
}
