<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository;

use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\UUID;

interface PostsRepositoryInterface
{
    public function get(UUID $uuid): Post;
    public function save(Post $post): void;
    public function delete(UUID $uuid): void;
}