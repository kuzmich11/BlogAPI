<?php

namespace KuznetsovVladimir\BlogApi\Http\Actions\Auth;

use DateTimeImmutable;
use KuznetsovVladimir\BlogApi\Blog\AuthToken;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\AuthException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use KuznetsovVladimir\BlogApi\Http\Actions\ActionInterface;
use KuznetsovVladimir\BlogApi\Http\Auth\TokenAuthenticationInterface;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\Response;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;

class Logout implements ActionInterface
{
    public function __construct(
        private TokenAuthenticationInterface $authentication,
        private AuthTokensRepositoryInterface   $authTokensRepository
    )
    {
    }

    /**
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $token = $this->authentication->token($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $authToken = new AuthToken(

                $token,
                $user->uuid(),
                (new DateTimeImmutable())
            );
        } catch (\Exception $e) {
        }

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => (string)$authToken,
        ]);
    }
}