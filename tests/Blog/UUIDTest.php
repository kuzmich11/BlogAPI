<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    public function testItConstruct()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("Malformed UUID: 123");

        new UUID('123');
    }

    public function testItGetToString(): void
    {
        $uuid = new UUID(UUID::random());

        $value = $uuid->__toString();

        $this->assertIsString($value);
    }

    public function testItGenerateUuid()
    {
        $uuid = new UUID(UUID::random());
        $value = $uuid->uuid();

        $this->assertNotNull($value);
    }

    public function testItGetUuid(): void
    {
        $uuid = new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4');

        $value = $uuid->uuid();

        $this->assertEquals('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4', $value);
    }


}