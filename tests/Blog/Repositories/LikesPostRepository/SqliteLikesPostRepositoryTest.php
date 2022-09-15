<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Repositories\LikesPostRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\LikeNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\LikePost;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository\SqliteLikesPostRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\UnitTests\logs\DummyLogger;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteLikesPostRepositoryTest extends TestCase
{
    public function testItSavesLikeToDatabase(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '311b6eb7-00a8-4b64-8ec8-168f8b3463e1',
                ':post_uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
                ':user_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteLikesPostRepository($connectionStub, new DummyLogger());
        $repository->save(
            new LikePost(
                new UUID('311b6eb7-00a8-4b64-8ec8-168f8b3463e1'),
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
                ),
                new User(
                    new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                    'ivan123',
                    '123456',
                    new Name('Ivan', 'Nikitin')
                ),
            ));
    }

    public function testItFindsLikeByUuid(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'post_uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'user_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'title' => 'Заголовок',
            'text' => 'Какой-то текст',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password' => '123456',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $likePostRepository = new SqliteLikesPostRepository($connectionStub, new DummyLogger());

        $post = $likePostRepository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));

        $this->assertSame('7b094211-1881-40f4-ac73-365ad0b2b2d4', (string)$post->uuid());
    }

    public function testItThrowsAnExceptionWhenLikeNotFound(): void
    {
        {

            $connectionStub = $this->createStub(PDO::class);
            $statementStub = $this->createStub(PDOStatement::class);
            $statementStub->method('fetch')->willReturn(false);
            $connectionStub->method('prepare')->willReturn($statementStub);
            $repository = new SqliteLikesPostRepository($connectionStub, new DummyLogger());

            $this->expectException(LikeNotFoundException::class);
            $this->expectExceptionMessage('Cannot find like: 7b094211-1881-40f4-ac73-365ad0b2b2d4');

            $repository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));
        }
    }

    public function testItFindsLikeByPostUuid(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'post_uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'user_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'title' => 'Заголовок',
            'text' => 'Какой-то текст',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password' => '123456',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $likePostRepository = new SqliteLikesPostRepository($connectionStub, new DummyLogger());

        $post = $likePostRepository->getByPostUuid(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));

        $this->assertSame('7b094211-1881-40f4-ac73-365ad0b2b2d4', (string)$post->uuid());
    }
}