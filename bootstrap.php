<?php

use KuznetsovVladimir\BlogApi\Blog\Container\DIContainer;
use KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository\LikesPostRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository\SqliteLikesPostRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(LikesPostRepositoryInterface::class,
SqliteLikesPostRepository::class);

return $container;