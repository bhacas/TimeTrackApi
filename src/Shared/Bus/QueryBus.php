<?php

namespace App\Shared\Bus;

class QueryBus
{
    private array $handlers = [];

    public function register(string $queryClass, callable $handler): void
    {
        $this->handlers[$queryClass] = $handler;
    }

    public function handle(object $query): mixed
    {
        $class = get_class($query);
        if (!isset($this->handlers[$class])) {
            throw new \RuntimeException("No handler for $class");
        }
        return call_user_func($this->handlers[$class], $query);
    }
}
