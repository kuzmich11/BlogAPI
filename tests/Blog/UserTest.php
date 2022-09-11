<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog;

use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function getCreateUser(): User
    {
        return new User(
            new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
            'admin',
            new Name('Ivan', 'Ivanov')
        );
    }

    public function testItGetUuid()
    {
        $user = $this->getCreateUser();

        $value = $user->uuid();

        $this->assertEquals('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4', $value);
    }

    public function testItGetUsername()
    {
        $user = $this->getCreateUser();

        $value = $user->username();

        $this->assertEquals('admin', $value);
    }

    public function testItGetName()
    {
        $user = $this->getCreateUser();

        $value = $user->name();
        $name = new Name('Ivan', 'Ivanov');

        $this->assertEquals($name, $value);
    }

    public function testItGetToString(): void
    {
        $user = $this->getCreateUser();

        $value = $user->__toString();

        $this->assertIsString($value);
    }

    public function testItSetUuid(): void
    {
        $user = $this->getCreateUser();

        $user->setUuid(new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'));
        $value = $user->Uuid();

        $this->assertEquals('38830eb6-d2cf-44f9-a7dd-5e7d634eac77', $value);
    }

    public function testItSetUsername(): void
    {
        $user = $this->getCreateUser();

        $user->setUsername('user');
        $value = $user->username();

        $this->assertEquals('user', $value);
    }

    public function testItSetName(): void
    {
        $user = $this->getCreateUser();

        $user->setName(new Name('Petr', 'Petrov'));
        $value = $user->name();
        $name = new Name('Petr', 'Petrov');

        $this->assertEquals($name, $value);
    }
}