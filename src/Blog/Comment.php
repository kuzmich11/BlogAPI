<?php

namespace KuznetsovVladimir\BlogApi\Blog;
use KuznetsovVladimir\BlogApi\User\User;


class Comment
{
    private int $id;
    private User $author;
    private Post $post;
    private string $textComment;

    /**
     * @param int $id
     * @param User $author
     * @param Post $post
     * @param string $textComment
     */
    public function __construct(int $id, User $author, Post $post, string $textComment)
    {
        $this->id = $id;
        $this->author = $author;
        $this->post = $post;
        $this->textComment = $textComment;
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
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getTextComment(): string
    {
        return $this->textComment;
    }

    /**
     * @param string $textComment
     */
    public function setTextComment(string $textComment): void
    {
        $this->textComment = $textComment;
    }

    public function __toString(): string
    {
        return "$this->textComment\n$this->author";
    }

}