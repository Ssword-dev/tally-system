<?php

namespace App\Router;

use App\Foundation\HeaderBag;

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
 * Response represents an HTTP response
 * Contains headers, status code, and content to be sent to the client
 */
final class Response
{
    /**
     * HTTP status code
     *
     * @var int
     */
    protected int $statusCode = 200;

    /**
     * HTTP reason phrase
     *
     * @var string
     */
    protected string $reasonPhrase = 'OK';

    /**
     * Response headers
     *
     * @var HeaderBag
     */
    protected HeaderBag $headers;

    /**
     * Response content
     *
     * @var string
     */
    protected string $content = '';

    private bool $responseSent;
    private bool $isFinal = false;

    /**
     * HTTP status codes and their reason phrases
     *
     * @var array
     */
    protected static array $statusTexts = [
        // 1xx Informational
        100 => 'Continue',
        101 => 'Switching Protocols',

        // 2xx Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',

        // 3xx Redirection
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',

        // 4xx Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        422 => 'Unprocessable Entity',

        // 5xx Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    /**
     * Constructor
     *
     * @param string $content The response content
     * @param int $statusCode The HTTP status code
     * @param array $headers Response headers
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->headers = new HeaderBag($headers);
        $this->responseSent = false;
        $this->isFinal = false;
        $this->setStatusCode($statusCode);
    }

    /**
     * Sets the HTTP status code
     *
     * @param int $code The HTTP status code
     * @param string|null $text The HTTP reason phrase (auto-detected if null)
     * @return self This response instance
     */
    public function setStatusCode(int $code, ?string $text = null): self
    {
        $this->statusCode = $code;

        if ($text === null) {
            $this->reasonPhrase = self::$statusTexts[$code] ?? 'Unknown';
        } else {
            $this->reasonPhrase = $text;
        }

        return $this;
    }

    /**
     * Gets the HTTP status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets the HTTP reason phrase
     *
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * Sets the response content
     *
     * @param string $content The content
     * @return self This response instance
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Gets the response content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Appends content to the response
     *
     * @param string $content The content to append
     * @return self This response instance
     */
    public function appendContent(string $content): self
    {
        $this->content .= $content;
        return $this;
    }

    /**
     * Sets a response header
     *
     * @param string $key The header name
     * @param string $value The header value
     * @param bool $replace Whether to replace existing header
     * @return self This response instance
     */
    public function setHeader(string $key, string $value, bool $replace = true): self
    {
        if ($replace) {
            $this->headers->set($key, $value);
        } elseif (!$this->headers->has($key)) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * Gets a response header
     *
     * @param string $key The header name
     * @param string|null $default The default value
     * @return string|null
     */
    public function getHeader(string $key, ?string $default = null): ?string
    {
        return $this->headers->get($key, $default);
    }

    /**
     * Gets all response headers
     *
     * @return HeaderBag
     */
    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists
     *
     * @param string $key The header name
     * @return bool
     */
    public function hasHeader(string $key): bool
    {
        return $this->headers->has($key);
    }

    /**
     * Removes a header
     *
     * @param string $key The header name
     * @return self This response instance
     */
    public function removeHeader(string $key): self
    {
        $this->headers->remove($key);
        return $this;
    }

    /**
     * Sets the Content-Type header
     *
     * @param string $mimeType The MIME type
     * @param string|null $charset The charset
     * @return self This response instance
     */
    public function setContentType(string $mimeType, ?string $charset = 'UTF-8'): self
    {
        $header = $mimeType;
        if ($charset) {
            $header .= '; charset=' . $charset;
        }
        $this->setHeader('Content-Type', $header);
        return $this;
    }

    /**
     * Gets the Content-Type header
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->getHeader('Content-Type');
    }

    /**
     * Sends the response to the client
     *
     * This method sends the status line and headers,
     * followed by the response content
     *
     * @return void
     */
    public function send(): void
    {
        // Send status line
        if (!headers_sent()) {
            header('HTTP/1.1 ' . $this->statusCode . ' ' . $this->reasonPhrase, true, $this->statusCode);
        }

        // Send headers
        foreach ($this->headers->all() as $name => $value) {
            if (!headers_sent()) {
                header($name . ': ' . $value, true);
            }
        }

        // Send content
        echo $this->content;

        $this->responseSent = true;
    }

    public function hasBeenSent()
    {
        return $this->responseSent;

    }

    public function finalize()
    {
        $this->isFinal = true;
    }

    public function isFinalResponse(): bool
    {
        return $this->isFinal;
    }

    /**
     * Creates a JSON response
     *
     * @param mixed $data The data to encode as JSON
     * @param int $statusCode The HTTP status code
     * @param array $headers Additional headers
     * @return self
     */
    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        $response = new self(json_encode($data), $statusCode, $headers);
        $response->setContentType('application/json');
        return $response;
    }

    /**
     * Creates a redirect response
     *
     * @param string $url The redirect target URL
     * @param int $statusCode The HTTP status code (default 302)
     * @return self
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->setHeader('Location', $url);
        return $response;
    }

    /**
     * Creates a not found response
     *
     * @param string $content Optional response content
     * @return self
     */
    public static function notFound(string $content = ''): self
    {
        return new self($content, 404);
    }

    /**
     * Creates an error response
     *
     * @param string $content Optional response content
     * @param int $statusCode The HTTP status code (default 500)
     * @return self
     */
    public static function error(string $content = '', int $statusCode = 500): self
    {
        return new self($content, $statusCode);
    }
}
