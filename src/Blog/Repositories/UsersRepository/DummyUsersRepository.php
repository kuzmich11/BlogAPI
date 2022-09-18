<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;

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

        return new User(UUID::random(), "user123", "123456", new Name("first", "last"));
    }

}