<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function get(UUID $uuid): Comment;
    public function save(Comment $comment): void;
}