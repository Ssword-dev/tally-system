<?php

namespace App\Router\Traits;

use Closure;

trait Groups
{
    public function group(array $options, Closure $callback): void
    {
        $oldPrefix = $this->groupPrefix;
        $oldMiddleware = $this->groupMiddleware;

        $this->groupPrefix = ($oldPrefix ?? '') . ($options['prefix'] ?? '');
        $this->groupMiddleware = array_merge(
            $oldMiddleware,
            $options['middleware'] ?? []
        );

        $callback($this);

        $this->groupPrefix = $oldPrefix;
        $this->groupMiddleware = $oldMiddleware;
    }
}
