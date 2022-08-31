<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests;

use PHPUnit\Framework\TestCase;

class HelloTest extends TestCase
{
    public function testItWorks(): void
    {
        $this->assertTrue(true);
    }
}

//Покрытие кода: php -d xdebug.mode=coverage vendor/bin/phpunit tests --coverage-html coverage_report --coverage-filter src