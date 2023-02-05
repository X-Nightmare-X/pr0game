<?php

class Singleton
{
    private static ?self $instance = null;
    private array $data = [];

    private function __construct()
    {
    }

    public static function load(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function &__get(string $name)
    {
        return $this->data[$name];
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = &$value;
    }

    public function __isset(string $name): bool
    {
        return $this->data[$name] !== null;
    }
}

function Singleton(): Singleton
{
    return Singleton::load();
}