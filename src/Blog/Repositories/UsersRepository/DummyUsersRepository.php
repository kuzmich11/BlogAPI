<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use KuznetsovVladimir\BlogApi\User\User;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {

    }
    public function get(UUID $uuid): User
    {

        throw new UserNotFoundException("Not found");
    }
    public function getByUsername(string $username): User
    {

        return new User(UUID::random(), "user123", new Name("first", "last"));
    }

}