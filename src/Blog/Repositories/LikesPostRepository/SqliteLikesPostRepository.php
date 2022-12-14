<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\LikeAlreadyExistsException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\LikeNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\LikePost;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteLikesPostRepository implements LikesPostRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger,
    )
    {
    }

    public function get(UUID $uuid): LikePost
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM postLikes WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getLike($statement, $uuid);
    }


    /**
     * @throws LikeNotFoundException
     * @throws InvalidArgumentException
     */
    private function getLike(PDOStatement $statement, string $uuid): LikePost
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            $this->logger->warning("Like {$uuid} not found");
            throw new LikeNotFoundException(
                "Cannot find like: $uuid"
            );
        }
        $usersRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $postsRepository = new SqlitePostsRepository($this->connection, $this->logger);
        return new LikePost(
            new UUID($result['uuid']),
            $postsRepository->get(new UUID($result['post_uuid'])),
            $usersRepository->get(new UUID($result['user_uuid'])),
        );
    }


    public function getByPostUuid(UUID $post_uuid): LikePost
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM postLikes WHERE post_uuid = :post_uuid'
        );
        $statement->execute([
            ':post_uuid' => (string)$post_uuid,
        ]);
        return $this->getLike($statement, $post_uuid);
    }

    public function save(LikePost $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO postLikes (uuid, post_uuid, user_uuid)
VALUES (:uuid, :post_uuid, :user_uuid)'
        );
        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':post_uuid' => $like->post()->uuid(),
            ':user_uuid' => $like->user()->uuid(),
        ]);

        $this->logger->info("Like created: {$like->uuid()}");
    }

    /**
     * @throws LikeAlreadyExistsException
     */
    public function checkLike(UUID $postUuid, string $userUuid)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM postLikes WHERE post_uuid = :post_uuid AND user_uuid = :user_uuid'
        );
        $statement->execute([
            ':post_uuid' => $postUuid->uuid(),
            ':user_uuid' => $userUuid
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return;
            }
        throw new LikeAlreadyExistsException ('Like already exists');
        }

}