<?php

namespace KuznetsovVladimir\BlogApi\Http\Actions\Posts;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\ActionInterface;
use KuznetsovVladimir\BlogApi\Http\Auth\IdentificationInterface;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\Response;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private IdentificationInterface $identification,
        private LoggerInterface $logger,
    ) {
    }
    public function handle(Request $request): Response
    {

        $author = $this->identification->user($request);

        $newPostUuid = UUID::random();
        try {

            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}