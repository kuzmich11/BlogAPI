<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Repositories\PostsRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\UnitTests\logs\DummyLogger;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItSavesPostToDatabase(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
                ':author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
                ':title' => 'Заголовок',
                ':text' => 'Какой-то текст',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());
        $repository->save(
            new Post(
                new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'),
                new User(
                    new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                    'ivan123',
                    '123456',
                    new Name('Ivan', 'Nikitin')
                ),
                'Заголовок',
                'Какой-то текст'
            ));
    }

    public function testItFindsPostByUuid(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'title' => 'Заголовок',
            'text' => 'Какой-то текст',
            'username' => 'ivan123',
            'password' => '123456',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $post = $postRepository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));

        $this->assertSame('7b094211-1881-40f4-ac73-365ad0b2b2d4', (string)$post->uuid());
    }
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        {

            $connectionStub = $this->createStub(PDO::class);
            $statementStub = $this->createStub(PDOStatement::class);
            $statementStub->method('fetch')->willReturn(false);
            $connectionStub->method('prepare')->willReturn($statementStub);
            $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

            $this->expectException(PostNotFoundException::class);
            $this->expectExceptionMessage('Cannot find post: 7b094211-1881-40f4-ac73-365ad0b2b2d4');

            $repository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));
        }
    }
}