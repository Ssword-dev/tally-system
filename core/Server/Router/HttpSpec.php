<?php
namespace App\Router;

use App\Traits\Singleton;

final class HttpSpec
{
    use Singleton;

    public array $methods = ["GET", "POST", "HEAD", "PUT", "PATCH", "DELETE", "OPTIONS"];
    public array $methodHashTable;

    public static function initInstance(HttpSpec $instance): void
    {
        $instance->methodHashTable = array_fill_keys($instance->methods, true);
    }

    public function isValidMethod(string $method): bool
    {
        $uppercaseMethod = strtoupper($method);
        return isset($this->methodHashTable[$uppercaseMethod]);
    }
}
