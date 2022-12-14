<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostsRepositoryException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger,
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

        $this->logger->info("Post created: {$post->uuid()}");
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts JOIN users ON posts.author_uuid=users.uuid WHERE posts.uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }


    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    public function getByPost(string $author_uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts JOIN users ON posts.author_uuid=users.uuid WHERE posts.author_uuid = :author_uuid'
        );
        $statement->execute([
            ':author_uuid' => $author_uuid,
        ]);

        return $this->getPost($statement, $author_uuid);
    }


    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    private function getPost(PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            $this->logger->warning("Post {$uuid} not found");
            throw new PostNotFoundException(
                "Cannot find post: $uuid"
            );
        }

        return new Post(
            new UUID($result['uuid']),
            new User(
                new UUID($result['uuid']),
                $result['username'],
                $result['password'],
                new Name($result['first_name'], $result['last_name'])
            ),
            $result['title'],
            $result['text'],
        );
    }

    /**
     * @throws PostsRepositoryException
     */
    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = :uuid'
            );
            $statement->execute([
                ':uuid' => (string)$uuid,
            ]);
        } catch (PDOException $e) {
            throw new PostsRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

    }
}