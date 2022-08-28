<?php

require_once __DIR__ . '/vendor/autoload.php';

use KuznetsovVladimir\BlogApi\Blog\Commands\Arguments;
use KuznetsovVladimir\BlogApi\Blog\Commands\CreateCommentCommand;
use KuznetsovVladimir\BlogApi\Blog\Commands\CreatePostCommand;
use KuznetsovVladimir\BlogApi\Blog\Commands\CreateUserCommand;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\AppException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteCommentsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\UUID;

//$faker = Faker\Factory::create('ru_RU');

$usersRepository = new SqliteUsersRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$postsRepository = new SqlitePostsRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));

$commentsRepository = new SqliteCommentsRepository(
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));

//Для ввода юзера из командной строки
//php cli.php username=ivan2 first_name=Ivan last_name=Nikitin

//$command = new CreateUserCommand($usersRepository);
//try {
//    $command->handle(Arguments::fromArgv($argv));
//}
//catch (AppException $e) {
//    echo "{$e->getMessage()}\n";
//}

//Для ввода поста из командной строки
//php cli.php author_uuid=5a91ed7a-0ae4-495f-b666-c52bc8f13fe4 title=Заголовок text='Какой-то текст'

//$command = new CreatePostCommand($postsRepository, $usersRepository);
//try {
//    $command->handle(Arguments::fromArgv($argv));
//}
//catch (AppException $e) {
//    echo "{$e->getMessage()}\n";
//}

//Для ввода комента из командной строки
//php cli.php post_uuid=e04e341e-2052-41b1-b929-d911e0cbe6f0 author_uuid=5a91ed7a-0ae4-495f-b666-c52bc8f13fe4 text='Какой-то текст'

$command = new CreateCommentCommand($postsRepository, $usersRepository, $commentsRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
}
catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}

//$comment = $commentsRepository->get(new UUID('baa7f30c-9f7f-476a-88be-9794e866950b'));
//echo $comment;