<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\AuthTokensRepository;

use KuznetsovVladimir\BlogApi\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}