<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Container;

class SomeClassWithParameter
{
// Класс с одним параметром
    public function __construct(
        private int $value
    ) {
    }
    public function value(): int
    {
        return $this->value;
    }

}