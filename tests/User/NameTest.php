<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\User;

use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testItGetFirstName(): void
    {
        $name = new Name('Ivan', 'Ivanov');

        $value = $name->first();

        $this->assertEquals('Ivan', $value);
    }

    public function testItGetLastName(): void
    {
        $name = new Name('Ivan', 'Ivanov');

        $value = $name->last();

        $this->assertEquals('Ivanov', $value);
    }

    public function testItGetToString(): void
    {
        $name = new Name('Ivan', 'Ivanov');

        $value = $name->__toString();

        $this->assertIsString($value);
    }

    public function testItSetFirstName(): void
    {
        $name = new Name('Ivan', 'Ivanov');

        $name->setFirstName('Nikolai');
        $value = $name->first();

        $this->assertEquals('Nikolai', $value);
    }

    public function testItSetLastName(): void
    {
        $name = new Name('Ivan', 'Ivanov');

        $name->setLastName('Petrov');
        $value = $name->last();

        $this->assertEquals('Petrov', $value);
    }
}