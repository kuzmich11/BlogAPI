<?php

use KuznetsovVladimir\BlogApi\Blog\Exceptions\AppException;
use KuznetsovVladimir\BlogApi\Http\Actions\Comments\CreateComment;
use KuznetsovVladimir\BlogApi\Http\Actions\LikesPost\CreateLikePost;
use KuznetsovVladimir\BlogApi\Http\Actions\Posts\CreatePost;
use KuznetsovVladimir\BlogApi\Http\Actions\Posts\DeletePost;
use KuznetsovVladimir\BlogApi\Http\Actions\Posts\FindPostByUuid;
use KuznetsovVladimir\BlogApi\Http\Actions\Users\FindUserByUsername;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Request;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindUserByUsername::class,
        '/posts/show' => FindPostByUuid::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/posts/like' => CreateLikePost::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();