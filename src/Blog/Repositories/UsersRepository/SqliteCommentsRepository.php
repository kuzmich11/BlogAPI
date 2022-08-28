<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\CommentNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use KuznetsovVladimir\BlogApi\User\User;
use PDO;
use PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{

    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(Comment $comment): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );
        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => $comment->post()->uuid(),
            ':author_uuid' => $comment->user()->uuid(),
            ':text' => $comment->text(),
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getComment($statement, $uuid);
    }


    public function getByComment(string $post_uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE author_uuid = :author_uuid'
        );
        $statement->execute([
            ':author_uuid' => $post_uuid,
        ]);
        return $this->getComment($statement, $post_uuid);
    }

    private function getComment(PDOStatement $statement, string $post_uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new CommentNotFoundException(
                "Cannot find post: $post_uuid"
            );
        }
        return new Comment(
            new UUID($result['uuid']),
            $this->getCommentUser($result['author_uuid']),
            $this->getCommentPost($result['post_uuid']),
            $result['text'],
        );
    }
    private function getCommentUser(string $author_uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :author_uuid'
        );
        $statement->execute([
            ':author_uuid' => $author_uuid,
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new UserNotFoundException(
                "Cannot find post: $author_uuid"
            );
        }
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );
    }

    private function getCommentPost(string $post_uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :post_uuid'
        );
        $statement->execute([
            ':post_uuid' => $post_uuid,
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new PostNotFoundException(
                "Cannot find post: $post_uuid"
            );
        }
        return new Post(
            new UUID($result['uuid']),
            $this->getCommentUser($result['author_uuid']),
            $result['title'],
            $result['text'],
        );
    }
}