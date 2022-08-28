<?php

namespace KuznetsovVladimir\BlogApi\Blog\Commands;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\UUID;

class CreateCommentCommand
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {

        $this->commentsRepository->save(new Comment(
                UUID::random(),
                $this->usersRepository->get(new UUID($arguments->get('author_uuid'))),
                $this->postsRepository->get(new UUID($arguments->get('post_uuid'))),
                $arguments->get('text'))
        );
    }
}