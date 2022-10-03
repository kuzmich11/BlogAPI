<?php

use Dotenv\Dotenv;
use KuznetsovVladimir\BlogApi\Blog\Container\DIContainer;
use KuznetsovVladimir\BlogApi\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository\LikesPostRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository\SqliteLikesPostRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Http\Auth\AuthenticationInterface;
use KuznetsovVladimir\BlogApi\Http\Auth\BearerTokenAuthentication;
use KuznetsovVladimir\BlogApi\Http\Auth\PasswordAuthentication;
use KuznetsovVladimir\BlogApi\Http\Auth\PasswordAuthenticationInterface;
use KuznetsovVladimir\BlogApi\Http\Auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$faker = new \Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
    \Faker\Generator::class,
    $faker
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . $_ENV['SQLITE_DB_PATH'])
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);


$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    LikesPostRepositoryInterface::class,
    SqliteLikesPostRepository::class);

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}


$container->bind(
    LoggerInterface::class,
    $logger
);


return $container;