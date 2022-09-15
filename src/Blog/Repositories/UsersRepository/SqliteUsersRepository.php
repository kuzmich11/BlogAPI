<?php

namespace KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger,
    )
    {
    }

    public function save(User $user): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
VALUES (:uuid, :username, :first_name, :last_name)'
        );
        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last(),
        ]);

        $this->logger->info("User created: {$user->uuid()}");
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement, $username);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $uuid): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            $this->logger->warning("User {$uuid} not found");
            throw new UserNotFoundException(
                "Cannot find user: $uuid"
            );
        }
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            new Name($result['first_name'], $result['last_name'])
        );
    }

}