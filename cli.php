<?php

use src\Person\Name;
use src\Blog\Post;
use src\Person\Person;

spl_autoload_register(function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});


$post = new Post(1, new Person(1, new Name(1,'Иван', 'Никитин'),
    new DateTimeImmutable()
),
'Всем привет!'
);
print $post;
//$name = new Name(1, "Петр", "Иванов");
//print $name;