<?php

namespace KuznetsovVladimir\BlogApi\Blog\Commands;

use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\UUID;

class CreatePostCommand
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {

        $this->postsRepository->save(new Post(
                UUID::random(),
                $this->usersRepository->get(new UUID($arguments->get('author_uuid'))),
                $arguments->get('title'),
                $arguments->get('text'))
        );
    }
}