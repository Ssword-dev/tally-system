<?php

namespace App\Router;

use Core\Server\Router\FileBag;
use Core\Server\Router\HeaderBag;
use Core\Server\Router\InputBag;
use Core\Server\Router\ParameterBag;
use Core\Server\Router\ServerBag;

/**
 * This class is adapted from Symfony HTTP Foundation:
 * https://github.com/symfony/http-foundation
 *
 * Original authors:
 *  - Fabien Potencier and contributors
 *
 * MIT License
 */

final class Request
{
    public readonly InputBag $request;
    public readonly InputBag $query;
    public readonly InputBag $cookies;
    public readonly FileBag $files;
    public readonly ServerBag $server;
    public readonly ParameterBag $attributes;
    public readonly HeaderBag $headers;

    // There are all the things i added on top of the original
    // implementation in the symfony http foundation to tailor it
    // more to my router.
    /**
     * The HTTP method of the request.
     * @author ssword-dev <ssword.dev@gmail.com>
     * @var string
     */
    public readonly string $method;

    /**
     * This is the path of the route requested.
     * @author ssword-dev <ssword.dev@gmail.com>
     * @var string
     */
    public readonly string $path;

    /**
     * A field temporarily assigned to the request while a route
     * tries to process the request and spit out a response.
     * @author ssword-dev <ssword.dev@gmail.com>
     * @var ParameterBag
     */
    public ParameterBag $routeParams;

    public function __construct(
        string $path,
        string $method,
        array $request,
        array $query,
        array $attributes,
        array $cookies,
        array $files,
        array $server
    ) {
        $this->path = $path;
        $this->method = $method;
        $this->request = new InputBag($request);
        $this->query = new InputBag($query);
        $this->cookies = new InputBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->attributes = new ParameterBag($attributes);
        $this->headers = new HeaderBag($this->extractHeaders($server));
        $this->routeParams = new ParameterBag([]);

    }

    public function useRouteParameters(array $parameters)
    {
        $this->routeParams->replace($parameters);
    }


    public static function fromGlobals()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return new self(
            $path,
            $_SERVER['REQUEST_METHOD'],
            $_POST,
            $_GET,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER,
        );
    }

    // A helper to extract headers from $_SERVER.
    // also modified by ssword-dev <ssword.dev@gmail.com>
    private function extractHeaders(array $server): array
    {
        $headers = [];

        // the original implementation does not use a hash
        // table. but! technically it is O(1) still, but i
        // decided to use a hash table cuz, why not.
        $unprefixedHeadersHashTable = array_fill_keys(
            ['CONTENT_TYPE', 'CONTENT_LENGTH'],
            true
        );

        foreach ($server as $key => $value) {
            // Convert HTTP_* to header name format (for example: HTTP_CONTENT_TYPE -> CONTENT-TYPE)
            // this check asks: does this key start with 'HTTP_'?
            if (strpos($key, 'HTTP_') === 0) {
                // You might be asking, why i did not normalize this to
                // proper case here? that is because, its redundant. it
                // gets lowercased in the header bag implementation
                // for case insensitive access.
                $headerKey = str_replace('_', '-', substr($key, 5));
                $headers[$headerKey] = $value;
            } elseif (isset($unprefixedHeadersHashTable[$key])) {
                $headerKey = str_replace('_', '-', $key);
                $headers[$headerKey] = $value;
            }
        }
        return $headers;
    }
}