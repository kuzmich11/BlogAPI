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
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');
        if ($this->userExists($username)) {
            $message = "User already exists: $username";
            $this->logger->warning($message);
            throw new CommandException($message);
        }

        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            $uuid,
            $username,
            new Name($arguments->get('first_name'), $arguments->get('last_name'))
        ));
//        $this->logger->info("User created: $uuid");
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