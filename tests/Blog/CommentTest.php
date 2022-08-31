<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use KuznetsovVladimir\BlogApi\User\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function getCreateComment(): Comment
    {
        return new Comment(
            new UUID('a51050c1-a658-4ddc-ba22-3b77a112f7df'),
            new User(
                new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                'admin',
                new Name('Ivan', 'Ivanov')
            ),
            new Post(
                new UUID('e04e341e-2052-41b1-b929-d911e0cbe6f0'),
                new User(
                    new UUID('c2c2b504-dfad-46ba-ba7c-a21558325d50'),
                    'user',
                    new Name('Petr', 'petrov')
                ),
                'Заголовок',
                'Текст'
            ),
            'Какой-то текст'
        );
    }

    public function testItGetUuid()
    {
        $comment = $this->getCreateComment();

        $value = $comment->uuid();

        $this->assertEquals('a51050c1-a658-4ddc-ba22-3b77a112f7df', $value);
    }

    public function testItGetUser()
    {
        $comment = $this->getCreateComment();

        $value = $comment->user();
        $user = new User(
            new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
            'admin',
            new Name('Ivan', 'Ivanov')
        );

        $this->assertEquals($user, $value);
    }

    public function testItGetPost()
    {
        $comment = $this->getCreateComment();

        $value = $comment->post();
        $post =  new Post(
            new UUID('e04e341e-2052-41b1-b929-d911e0cbe6f0'),
            new User(
                new UUID('c2c2b504-dfad-46ba-ba7c-a21558325d50'),
                'user',
                new Name('Petr', 'petrov')
            ),
            'Заголовок',
            'Текст'
        );

        $this->assertEquals($post, $value);
    }

    public function testItGetText()
    {
        $comment = $this->getCreateComment();

        $value = $comment->text();

        $this->assertEquals('Какой-то текст', $value);
    }

    public function testItGetToString(): void
    {
        $comment = $this->getCreateComment();

        $value = $comment->__toString();

        $this->assertIsString($value);
    }

    public function testItSetUuid()
    {
        $comment = $this->getCreateComment();

        $comment->setUuid(new UUID('52763e12-b5b8-4d17-961b-334bbc2fa686'));
        $value = $comment->uuid();

        $this->assertEquals('52763e12-b5b8-4d17-961b-334bbc2fa686', $value);
    }

    public function testItSetUser()
    {
        $comment = $this->getCreateComment();

        $comment->setUser(
            new User(
            new UUID('36086b79-11a7-4962-8b09-a880651f0f58'),
            'user',
            new Name('Petr', 'Petrov')
        ));
        $value = $comment->user();
        $user = new User(
            new UUID('36086b79-11a7-4962-8b09-a880651f0f58'),
            'user',
            new Name('Petr', 'Petrov')
        );

        $this->assertEquals($user, $value);
    }

    public function testItSetPost()
    {
        $comment = $this->getCreateComment();

        $comment->setPost(
            new Post(
                new UUID('c2c2b504-dfad-46ba-ba7c-a21558325d50'),
                new User(
                    new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                    'admin',
                    new Name('Ivan', 'Ivanov')
                ),
                'Новый заголовок',
                'Новый текст'
        ));
        $value = $comment->post();
        $post =  new Post(
            new UUID('c2c2b504-dfad-46ba-ba7c-a21558325d50'),
            new User(
                new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                'admin',
                new Name('Ivan', 'Ivanov')
            ),
            'Новый заголовок',
            'Новый текст'
        );

        $this->assertEquals($post, $value);
    }

    public function testItSetText()
    {
        $comment = $this->getCreateComment();

        $comment->setText('Другой текст');
        $value = $comment->text();

        $this->assertEquals('Другой текст', $value);
    }

}