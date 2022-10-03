<?php

require_once __DIR__ . '/vendor/autoload.php';

use KuznetsovVladimir\BlogApi\Blog\Commands\FakeData\PopulateDB;
use KuznetsovVladimir\BlogApi\Blog\Commands\Posts\DeletePost;
use KuznetsovVladimir\BlogApi\Blog\Commands\Users\CreateUser;
use KuznetsovVladimir\BlogApi\Blog\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];
foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

try {
    $application->run();
} catch (Exception $e) {
}


//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (AppException $e) {
//    $logger->error($e->getMessage(), ['exception' => $e]);
//}
