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
 * FileBag represents HTTP request file parameters ($_FILES)
 * Extends ParameterBag to provide file upload data
 * 
 * This is actually not different than parameter bag. but
 * i keep this just in case i need to specialize access on $_FILES.
 */
final class FileBag extends ParameterBag
{
    /**
     * Constructor
     *
     * @param array $parameters Initial parameters from $_FILES
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }
}
