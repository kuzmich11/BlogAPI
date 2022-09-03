<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Http\Actions\Comments;

use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\CommentNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\PostNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\Comments\CreateComment;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\SuccessfulResponse;
use KuznetsovVladimir\BlogApi\User\Name;
use PHPUnit\Framework\TestCase;

class CreateCommentTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(
            [],
            [],
            '{
            "author_uuid": "38830eb6-d2cf-44f9-a7dd-5e7d634eac77",
            "post_uuid": "52763e12-b5b8-4d17-961b-334bbc2fa686",
            "text": "TEXT"
            }'
        );

        $usersRepository = $this->usersRepository([new User (
            new UUID('38830eb6-d2cf-44f9-a7dd-5e7d634eac77'),
            'ivan',
            new Name('Ivan', 'Nikitin')
        )]);

        $postsRepository = $this->postsRepository([new Post(
            new UUID('52763e12-b5b8-4d17-961b-334bbc2fa686'),
            new User(
                new UUID('5a91ed7a-0ae4-495f-b666-c52bc8f13fe4'),
                'ivan2',
                new Name('Ivan', 'Nikitin')
            ),
            'Заголовок',
            'Какой-то текст'
        )]);

        $commentsRepository = $this->commentsRepository([]);

        $action = new CreateComment($commentsRepository, $postsRepository, $usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString("{\"success\":true,\"data\":{\"uuid\":\"{$commentsRepository->returnUuid(0)}\"}}");

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
            "author_uuid": "38830eb6-d2cf-44f9-a7dd-5e7d634eac7",
            "post_uuid": "52763e12-b5b8-4d17-961b-334bbc2fa686",
            "text": "TEXT"
            }'
        );
        $usersRepository = $this->usersRepository([]);

        $postsRepository = $this->postsRepository([]);

        $commentsRepository = $this->commentsRepository([]);

        $action = new CreateComment($commentsRepository, $postsRepository, $usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            "{\"success\":false,\"reason\":\"Malformed UUID: 38830eb6-d2cf-44f9-a7dd-5e7d634eac7\"}");

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnErrorIFUserNotFound(): void
    {
        $request = new Request(
            [],
            [],
            '{
            "author_uuid": "38830eb6-d2cf-44f9-a7dd-5e7d634eac77",
            "post_uuid": "52763e12-b5b8-4d17-961b-334bbc2fa686",
            "text": "TEXT"
            }'
        );
        $usersRepository = $this->usersRepository([]);

        $postsRepository = $this->postsRepository([]);

        $commentsRepository = $this->commentsRepository([]);

        $action = new CreateComment($commentsRepository, $postsRepository, $usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            "{\"success\":false,\"reason\":\"User not found\"}");

        $response->send();
    }

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
        $usersRepository = $this->usersRepository([]);

        $postsRepository = $this->postsRepository([]);

        $commentsRepository = $this->commentsRepository([]);

        $action = new CreateComment($commentsRepository, $postsRepository, $usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            "{\"success\":false,\"reason\":\"No such field: author_uuid\"}");

        $response->send();
    }

    private function commentsRepository(array $comments):  CommentsRepositoryInterface
    {
        return new class ($comments) implements CommentsRepositoryInterface
        {
            public function __construct(
                private array $comments
            ) {
            }
            public function save(Comment $comment): void
            {
               $this->comments[0]=$comment;
            }

            public function get(UUID $uuid): Comment
            {
                foreach ($this->comments as $comment) {
                    if ($comment instanceof Comment && $uuid === $comment->uuid())
                    {
                        return $comment;
                    }
                }
                throw new CommentNotFoundException("Not found");
            }
            public function returnUuid(int $number): string
            {
                return $this->comments[$number]->uuid();
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
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
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

}