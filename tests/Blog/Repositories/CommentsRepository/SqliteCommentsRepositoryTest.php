<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Repositories\CommentsRepository;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\CommentNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use KuznetsovVladimir\BlogApi\Blog\UnitTests\logs\DummyLogger;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '311b6eb7-00a8-4b64-8ec8-168f8b3463e1',
                ':post_uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
                ':author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
                ':text' => 'Какой-то текст',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $repository->save(
            new Comment(
                new UUID('311b6eb7-00a8-4b64-8ec8-168f8b3463e1'),
                new User(
                    new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                    'ivan123',
                    '123465',
                    new Name('Ivan', 'Nikitin')
                ),
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
                'Какой-то текст',
            ));
    }

    public function testItFindsCommentByUuid(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '311b6eb7-00a8-4b64-8ec8-168f8b3463e1',
            'post_uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'text' => 'Какой-то текст',
            'title' => 'Заголовок',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password' => '123456',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

        $post = $commentRepository->get(new UUID('311b6eb7-00a8-4b64-8ec8-168f8b3463e1'));

        $this->assertSame('311b6eb7-00a8-4b64-8ec8-168f8b3463e1', (string)$post->uuid());
    }

    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        {

            $connectionStub = $this->createStub(PDO::class);
            $statementStub = $this->createStub(PDOStatement::class);
            $statementStub->method('fetch')->willReturn(false);
            $connectionStub->method('prepare')->willReturn($statementStub);
            $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

            $this->expectException(CommentNotFoundException::class);
            $this->expectExceptionMessage('Cannot find comment: 7b094211-1881-40f4-ac73-365ad0b2b2d4');

            $repository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));
        }
    }
}