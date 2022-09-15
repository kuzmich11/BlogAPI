<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Repositories\UserRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\UnitTests\logs\DummyLogger;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{


    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        $repository->save(
            new User(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                'ivan123',
                new Name('Ivan', 'Nikitin')
            )
        );
    }

    public function testItFindsUserByUuid(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '38830eb6-d2cf-44f9-a7dd-5e7d634eac77',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $post = $userRepository->get(new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'));

        $this->assertSame('38830eb6-d2cf-44f9-a7dd-5e7d634eac77', (string)$post->uuid());
    }

    public function testItFindsUserByUser(): void
    {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '38830eb6-d2cf-44f9-a7dd-5e7d634eac77',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $post = $userRepository->getByUsername('ivan123');

        $this->assertSame('ivan123', (string)$post->username());
    }

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {


        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);
        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');

        $repository->getByUsername('Ivan');

    }
}