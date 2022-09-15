<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Http\Actions\LikesPost;

use DateTimeImmutable;
use KuznetsovVladimir\BlogApi\Blog\AuthToken;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\LikeNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\LikePost;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\LikesPostRepository\LikesPostRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\LikesPost\CreateLikePost;
use KuznetsovVladimir\BlogApi\Http\Auth\TokenAuthenticationInterface;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;
use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;

class CreateLikePostTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(
            [],
            ["HTTP_AUTHORIZATION"=>"123456"],
            '{
            "post_uuid": "52763e12-b5b8-4d17-961b-334bbc2fa686"
            }'
        );

        $usersRepository = $this->usersRepository([new User (
            new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            'ivan',
            '123456',
            new Name('Ivan', 'Nikitin')
        )]);

        $postsRepository = $this->postsRepository([new Post(
            new UUID('52763e12-b5b8-4d17-961b-334bbc2fa686'),
            new User(
                new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                'ivan2',
                '123456',
                new Name('Ivan', 'Nikitin')
            ),
            'Заголовок',
            'Какой-то текст'
        )]);

        $likesPostRepository = $this->likesPostRepository([]);

        $authTokensRepository = $this->authTokensRepository([
            '123456',
            new UUID ('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            new DateTimeImmutable()
        ]);

        $tokenAuthentication = $this->tokenAuthentication($authTokensRepository, $usersRepository);

        $action = new CreateLikePost($likesPostRepository, $postsRepository, $tokenAuthentication);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString("{\"success\":true,\"data\":{\"uuid\":\"{$likesPostRepository->returnUuid(0)}\"}}");

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnErrorIFInvalidFormatUuid(): void
    {
        $request = new Request(
            [],
            [],
            '{
            "post_uuid": "52763e12-b5b8-4d17-961b-334bbc2fa68"
            }'
        );
        $usersRepository = $this->usersRepository([new User (
            new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            'ivan',
            '123456',
            new Name('Ivan', 'Nikitin')
        )]);

        $postsRepository = $this->postsRepository([]);

        $likesPostRepository = $this->likesPostRepository([]);

        $authTokensRepository = $this->authTokensRepository([
            '123456',
            new UUID ('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            new DateTimeImmutable()
        ]);

        $tokenAuthentication = $this->tokenAuthentication($authTokensRepository, $usersRepository);

        $action = new CreateLikePost($likesPostRepository, $postsRepository, $tokenAuthentication);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            "{\"success\":false,\"reason\":\"Malformed UUID: 52763e12-b5b8-4d17-961b-334bbc2fa68\"}");

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnErrorIFNotAllData(): void
    {
        $request = new Request(
            [],
            [],
            '{
            }'
        );
        $usersRepository = $this->usersRepository([new User (
            new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            'ivan',
            '123456',
            new Name('Ivan', 'Nikitin'))
        ]);

        $postsRepository = $this->postsRepository([]);

        $likesPostRepository = $this->likesPostRepository([]);

        $authTokensRepository = $this->authTokensRepository([
            '123456',
            new UUID ('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            new DateTimeImmutable()
        ]);

        $tokenAuthentication = $this->tokenAuthentication($authTokensRepository, $usersRepository);

        $action = new CreateLikePost($likesPostRepository, $postsRepository, $tokenAuthentication);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            "{\"success\":false,\"reason\":\"No such field: post_uuid\"}");

        $response->send();
    }

    private function likesPostRepository(array $likesPost):  LikesPostRepositoryInterface
    {
        return new class ($likesPost) implements LikesPostRepositoryInterface
        {
            public function __construct(
                private array $likesPost
            ) {
            }
            public function save(LikePost $like): void
            {
                $this->likesPost[0]=$like;
            }

            public function get(UUID $uuid): LikePost
            {
                foreach ($this->likesPost as $like) {
                    if ($like instanceof LikePost && $uuid === $like->uuid())
                    {
                        return $like;
                    }
                }
                throw new LikeNotFoundException("Not found");
            }
            public function returnUuid(int $number): string
            {
                return $this->likesPost[$number]->uuid();
            }

            public function getByPostUuid(UUID $post_uuid): LikePost
            {
                throw new LikeNotFoundException("Not found");
            }
            public function checkLike(UUID $postUuid, string $userUuid) {

            }
        };

    }

    private function usersRepository(array $users):  UsersRepositoryInterface
    {
        return new class ($users) implements usersRepositoryInterface
        {
            public function __construct(
                private array $users
            ) {
            }
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $uuid == $user->uuid())
                    {
                        return $user;
                    }
                }
                throw new UserNotFoundException("User not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }

    private function postsRepository(array $posts):  PostsRepositoryInterface
    {
        return new class ($posts) implements PostsRepositoryInterface
        {
            public function __construct(
                private array $posts
            ) {
            }
            public function save(Post $posts): void
            {

            }

            public function get(UUID $uuid): Post
            {
                foreach ($this->posts as $post) {
                    if ($post instanceof Post && $uuid == $post->uuid())
                    {
                        return $post;
                    }
                }
                throw new PostNotFoundException("Not found");
            }
            public function delete(UUID $uuid): void
            {
            }

        };
    }

    private function tokenAuthentication($authTokensRepository, $usersRepository): TokenAuthenticationInterface
    {
        return new class ($authTokensRepository, $usersRepository) implements TokenAuthenticationInterface
        {
            public function __construct(
                private $authTokensRepository,
                private $userRepository
            ) {
            }

            public function user(Request $request): User
            {
                return $this->userRepository->get(new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'));
            }
        };
    }

    private function authTokensRepository(array $array): AuthTokensRepositoryInterface
    {
        return new class ($array) implements AuthTokensRepositoryInterface
        {
            public function __construct(
                private array $array
            ) {
            }

            public function save(AuthToken $authToken): void
            {
            }

            public function get(string $token): AuthToken
            {
                foreach ($this->array as $authToken) {
                    if ($authToken instanceof AuthToken && $token == $authToken->token())
                    {
                        return $authToken;
                    }
                }
                throw new PostNotFoundException("Not found");
            }
        };
    }
}