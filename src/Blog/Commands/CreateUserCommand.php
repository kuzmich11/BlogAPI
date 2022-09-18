<?php

namespace KuznetsovVladimir\BlogApi\Blog\Commands;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\ArgumentsException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\CommandException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use Psr\Log\LoggerInterface;


class CreateUserCommand
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface          $logger,
    )
    {
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");
        $username = $arguments->get('username');
        if ($this->userExists($username)) {
            $message = "User already exists: $username";
            throw new CommandException($message);
            $this->logger->warning($message);
            return;
        }

        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );
        $this->usersRepository->save($user);

        $this->logger->info('User created: ' . $user->uuid());
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }

}