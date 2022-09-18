<?php

namespace KuznetsovVladimir\BlogApi\Http\Auth;

use DateTimeImmutable;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\AuthException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\AuthTokenNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Blog\User;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoryInterface      $usersRepository,
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function token(Request $request): string
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        return mb_substr($header, strlen(self::HEADER_PREFIX));

    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        $token = $this->token($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }

        $userUuid = $authToken->userUuid();

        return $this->usersRepository->get($userUuid);
    }
}