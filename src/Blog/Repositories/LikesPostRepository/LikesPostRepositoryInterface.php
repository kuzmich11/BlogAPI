<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository;

use KuznetsovVladimir\BlogApi\Blog\LikePost;
use KuznetsovVladimir\BlogApi\Blog\UUID;

interface LikesPostRepositoryInterface
{
    public function get(UUID $uuid): LikePost;
    public function save(LikePost $like): void;
    public function getByPostUuid(UUID $post_uuid): LikePost;
}