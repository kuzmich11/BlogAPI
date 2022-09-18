<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog;

use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function getCreatePost(): Post
    {
        return new Post(
            new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'),
            new User(
                new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                'admin',
                '123456',
                new Name('Ivan', 'Ivanov')
            ),
            'Заголовок',
            'Какой-то текст'
        );
    }

    public function testItGetUuid()
    {
        $post = $this->getCreatePost();

        $value = $post->uuid();

        $this->assertEquals('7b094211-1881-40f4-ac73-365ad0b2b2d4', $value);
    }

    public function testItGetUser()
    {
        $post = $this->getCreatePost();

        $value = $post->user();
        $user = new User(
            new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
            'admin',
            '123456',
            new Name('Ivan', 'Ivanov')
        );

        $this->assertEquals($user, $value);
    }

    public function testItGetTitle()
    {
        $post = $this->getCreatePost();

        $value = $post->title();

        $this->assertEquals('Заголовок', $value);
    }

    public function testItGetText()
    {
        $post = $this->getCreatePost();

        $value = $post->text();

        $this->assertEquals('Какой-то текст', $value);
    }

    public function testItGetToString(): void
    {
        $post = $this->getCreatePost();

        $value = $post->__toString();

        $this->assertIsString($value);
    }

    public function testItSetUuid()
    {
        $post = $this->getCreatePost();

        $post->setUuid(new UUID('52763e12-b5b8-4d17-961b-334bbc2fa686'));
        $value = $post->uuid();

        $this->assertEquals('52763e12-b5b8-4d17-961b-334bbc2fa686', $value);
    }

    public function testItSetUser()
    {
        $post = $this->getCreatePost();

        $post->setUser(new User(
            new UUID('e04e341e-2052-41b1-b929-d911e0cbe6f0'),
            'user',
            '123456',
            new Name('Petr', 'Petrov')
        ));
        $value = $post->user();
        $user = new User(
            new UUID('e04e341e-2052-41b1-b929-d911e0cbe6f0'),
            'user',
            '123456',
            new Name('Petr', 'Petrov')
        );

        $this->assertEquals($user, $value);
    }

    public function testItSetTitle()
    {
        $post = $this->getCreatePost();

        $post->setTitle('Другой заголовок');
        $value = $post->title();

        $this->assertEquals('Другой заголовок', $value);
    }

    public function testItSetText()
    {
        $post = $this->getCreatePost();

        $post->setText('Другой текст');
        $value = $post->text();

        $this->assertEquals('Другой текст', $value);
    }
}