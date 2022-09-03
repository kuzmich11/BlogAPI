<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(Post $post): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
VALUES (:uuid, :author_uuid, :title, :text)'
        );
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => $post->user()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text(),
        ]);
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }


//    public function getByPost(string $author_uuid): Post
//    {
//        $statement = $this->connection->prepare(
//            'SELECT * FROM posts WHERE author_uuid = :author_uuid'
//        );
//        $statement->execute([
//            ':author_uuid' => $author_uuid,
//        ]);
//
//        return $this->getPost($statement, $author_uuid);
//    }

    private function getPost(PDOStatement $statement, string $author_uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $author_uuid"
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection);
        return new Post(
            new UUID($result['uuid']),
            $usersRepository->get(new UUID($result['author_uuid'])),
            $result['title'],
            $result['text'],
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
    }
}