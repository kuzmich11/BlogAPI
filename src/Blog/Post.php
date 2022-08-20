<?php

namespace KuznetsovVladimir\BlogApi\Blog;
use KuznetsovVladimir\BlogApi\User\User;

class Post
{
    private int $id;
    private User $author;
    private string $header;
    private string $text;

    /**
     * @param int $id
     * @param User $author
     * @param string $header
     * @param string $text
     */
    public function __construct(int $id, User $author, string $header, string $text)
    {
        $this->id = $id;
        $this->author = $author;
        $this->header = $header;
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return "$this->header\n$this->text\nАвтор статьи: $this->author";
    }

}