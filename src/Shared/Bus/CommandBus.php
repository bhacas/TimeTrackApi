<?php

namespace App\Shared\Bus;

class CommandBus
{
    private array $handlers = [];

    public function register(string $commandClass, callable $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    public function handle(object $command): mixed
    {
        $class = get_class($command);
        if (!isset($this->handlers[$class])) {
            throw new \RuntimeException("No handler for $class");
        }
        return call_user_func($this->handlers[$class], $command);
    }
}
