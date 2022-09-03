<?php

namespace KuznetsovVladimir\BlogApi\Http\Actions\Posts;

use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\InvalidArgumentException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\UserNotFoundException;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\Http\Actions\ActionInterface;
use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\Response;
use KuznetsovVladimir\BlogApi\Http\ErrorResponse;

class CreatePost implements ActionInterface
{

    /**
     * @param SqlitePostsRepository $postsRepository
     * @param SqliteUsersRepository $usersRepository
     */
    public function __construct(
        private SqlitePostsRepository $postsRepository,
        private SqliteUsersRepository $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $author = new User(
                new UUID($request->jsonBodyField('author_uuid')),

            );
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newPostUuid = UUID::random();
        try {

            $post = new Post(
                $newPostUuid,
                $authorUuid,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);
// Возвращаем успешный ответ,
// содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}