<?php
namespace Core\Server\Cache;

/**
 * @template K
 * @template V
 */
abstract class Cache
{
    /**
     * @param K $key
     * @return void
     */
    abstract public function has($key);
    /**
     * @param K $key
     * @param V $default
     * @return void
     */
    abstract public function get($key, $default = null);

    /**
     * @param K $key
     * @param V $value
     * @return void
     */
    abstract public function set($key, $value);

    /**
     * @param K $key
     * @return void
     */
    abstract public function delete($key);
}