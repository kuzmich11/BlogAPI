<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Commands;

use KuznetsovVladimir\BlogApi\Blog\Commands\Arguments;
use KuznetsovVladimir\BlogApi\Blog\Commands\CreateUserCommand;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\ArgumentsException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\CommandException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\DummyUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\User;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUsersRepository());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('User already exists: Ivan');

        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {

                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {

                throw new UserNotFoundException("Not found");
            }
        };
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
        ]));
    }

    public function testItRequiresFirstName(): void
    {

        $command = new CreateUserCommand($this->makeUsersRepository());

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    public function testItSavesUserToRepository(): void
    {

        $usersRepository = new class implements UsersRepositoryInterface {

            private bool $called = false;

            public function save(User $user): void
            {

                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository);

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }
}