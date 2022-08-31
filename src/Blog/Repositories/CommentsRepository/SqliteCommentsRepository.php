<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\CommentNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\UUID;
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


//    public function getByComment(string $post_uuid): Comment
//    {
//        $statement = $this->connection->prepare(
//            'SELECT * FROM posts WHERE author_uuid = :author_uuid'
//        );
//        $statement->execute([
//            ':author_uuid' => $post_uuid,
//        ]);
//        return $this->getComment($statement, $post_uuid);
//    }

    private function getComment(PDOStatement $statement, string $post_uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new CommentNotFoundException(
                "Cannot find comment: $post_uuid"
            );
        }
        $usersRepository = new SqliteUsersRepository($this->connection);
        $postsRepository = new SqlitePostsRepository($this->connection);
        return new Comment(
            new UUID($result['uuid']),
            $usersRepository->get(new UUID($result['author_uuid'])),
            $postsRepository->get(new UUID($result['post_uuid'])),
            $result['text'],
        );
    }
}