<?php

require_once __DIR__ . '/vendor/autoload.php';

use KuznetsovVladimir\BlogApi\Blog\Commands\Arguments;
use KuznetsovVladimir\BlogApi\Blog\Commands\CreateUserCommand;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\AppException;

//$faker = Faker\Factory::create('ru_RU');

$container = require __DIR__ . '/bootstrap.php';
// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}
