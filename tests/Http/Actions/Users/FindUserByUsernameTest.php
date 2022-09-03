<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Http\Actions\Users;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\Users\FindUserByUsername;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;
use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;

class FindUserByUsernameTest extends TestCase
{

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {

        $request = new Request([], [], '');
        $usersRepository = $this->usersRepository([]);
        $action = new FindUserByUsername($usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');

        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsErrorResponseIfUserNotFound(): void
    {

        $request = new Request(['username' => 'ivan'], [], '');
        $usersRepository = $this->usersRepository([]);
        $action = new FindUserByUsername($usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');

        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');

        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                'ivan',
                new Name('Ivan', 'Nikitin')
            ),
        ]);
        $action = new FindUserByUsername($usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');

        $response->send();
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {

        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
            ) {
            }
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->username())
                    {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }

}