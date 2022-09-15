<?php

require_once __DIR__ . '/vendor/autoload.php';

use KuznetsovVladimir\BlogApi\Blog\Commands\Arguments;
use KuznetsovVladimir\BlogApi\Blog\Commands\CreateUserCommand;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

//$faker = Faker\Factory::create('ru_RU');

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
}
