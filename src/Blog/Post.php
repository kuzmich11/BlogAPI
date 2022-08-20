<?php

namespace src\Blog;
use src\Person\Person;

class Post
{
    private int $id;
    private Person $author;
    private string $text;

    /**
     * @param int $id
     * @param Person $author
     * @param string $text
     */
    public function __construct(int $id, Person $author, string $text)
    {
        $this->id = $id;
        $this->author = $author;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->author . ' пишет: ' . $this->text;
    }

}