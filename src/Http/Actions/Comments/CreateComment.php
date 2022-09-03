<?php

namespace KuznetsovVladimir\BlogApi\Http\Actions\Comments;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\ActionInterface;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\Response;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {
    }
    public function handle(Request $request): Response
    {

        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse('User not found');
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse('Post not found');
        }

        $newCommentUuid = UUID::random();
        try {

            $comment = new Comment(
                $newCommentUuid,
                $this->usersRepository->get($authorUuid),
                $this->postsRepository->get($postUuid),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}