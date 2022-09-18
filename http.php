<?php

use KuznetsovVladimir\BlogApi\Blog\Exceptions\AppException;
use KuznetsovVladimir\BlogApi\Http\Actions\Auth\LogIn;
use KuznetsovVladimir\BlogApi\Http\Actions\Auth\Logout;
use KuznetsovVladimir\BlogApi\Http\Actions\Comments\CreateComment;
use KuznetsovVladimir\BlogApi\Http\Actions\LikesPost\CreateLikePost;
use KuznetsovVladimir\BlogApi\Http\Actions\Posts\CreatePost;
use KuznetsovVladimir\BlogApi\Http\Actions\Posts\DeletePost;
use KuznetsovVladimir\BlogApi\Http\Actions\Posts\FindPostByUuid;
use KuznetsovVladimir\BlogApi\Http\Actions\Users\FindUserByUsername;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Request;
use Psr\Log\LoggerInterface;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;


$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    try {
        (new ErrorResponse)->send();
    } catch (JsonException $e) {
    }
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    try {
        (new ErrorResponse)->send();
    } catch (JsonException $e) {
    }
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindUserByUsername::class,
        '/posts/show' => FindPostByUuid::class,
        '/logout' => Logout::class
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/posts/like' => CreateLikePost::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ]
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    try {
        (new ErrorResponse($message))->send();
    } catch (JsonException $e) {
    }
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    try {
        (new ErrorResponse($e->getMessage()))->send();
    } catch (JsonException $e) {
    }
}

