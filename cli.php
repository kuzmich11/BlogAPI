<?php

require_once __DIR__ . '/vendor/autoload.php';

use KuznetsovVladimir\BlogApi\User\User;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Comment;

//spl_autoload_register(function ($class) {
//    $file = str_ireplace('KuznetsovVladimir\BlogApi', 'src', $class);
//    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file) . '.php';
//    if (file_exists($file)) {
//        require $file;
//    }
//});

$faker = Faker\Factory::create('ru_RU');
if (isset($argv[1])) {


    if ($argv[1] === 'user') {
        $user = new User(1, $faker->lastName(), $faker->firstName());
        echo $user;
    }
    if ($argv[1] === 'post') {
        $post = new Post(1,
            new User(1, $faker->firstName(), $faker->lastName()), $faker->sentence(3), $faker->paragraphs(3, true));
        echo $post;
    }
    if ($argv[1] === 'comment') {
        $comment = new Comment(1,
            $user = new User (1, $faker->lastName(), $faker->firstName()),
            new Post(1, $user, $faker->sentence(3), $faker->paragraphs(3, true)),
            $faker->paragraphs(1, true)
        );
        echo $comment;
    }
}
//$post = new Post(1, new User(1, 'Иван', 'Никитин'), "Заголовок", "Текст статьи"
//);
//print $post;

