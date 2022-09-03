<?php

namespace KuznetsovVladimir\BlogApi\Http\Actions\Posts;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\ActionInterface;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Response;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $this->postsRepository->delete($uuid);
        } catch (postNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Delete' => "Статья {$request->query('uuid')} удалена"
        ]);

    }
}