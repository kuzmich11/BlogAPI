<?php

namespace KuznetsovVladimir\BlogApi\Http\Auth;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\AuthException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Http\Request;

class JsonBodyUsernameIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }
    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }

}