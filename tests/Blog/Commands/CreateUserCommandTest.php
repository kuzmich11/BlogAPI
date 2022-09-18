<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Commands;

use KuznetsovVladimir\BlogApi\Blog\Commands\Users\CreateUser;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase
{

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name, last_name, password"');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );

    }

    /**
     * @throws ExceptionInterface
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
//        $usersRepository = new DummyUsersRepository();

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
                return new User(UUID::random(), "user123", "123456", new Name("first", "last"));
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUser(
            $usersRepository,
        );

        $command->run(
            new ArrayInput([
                'username' => 'user123',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Ivanov'
            ]),
            new NullOutput()
        );
        $this->assertFalse($usersRepository->wasCalled());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresLastName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "last_name").'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresFirstName(): void
    {

        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name, last_name").');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );

    }

    /**
     * @throws ExceptionInterface
     */
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

        $command = new CreateUser(
            $usersRepository,
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
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
}