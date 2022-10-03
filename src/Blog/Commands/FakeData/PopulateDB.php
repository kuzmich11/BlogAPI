<?php

namespace KuznetsovVladimir\BlogApi\Blog\Commands\FakeData;

use Faker\Generator;
use KuznetsovVladimir\BlogApi\Blog\Comment;
use KuznetsovVladimir\BlogApi\Blog\Post;
use KuznetsovVladimir\BlogApi\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Blog\UUID;
use KuznetsovVladimir\BlogApi\User\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class PopulateDB extends Command
{
    public function __construct(
        private Generator                   $faker,
        private UsersRepositoryInterface    $usersRepository,
        private PostsRepositoryInterface    $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Users number',
                '10'
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Posts number',
                '20'
            )
            ->
            addOption(
                'comments-number',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Comments number',
                '5'
            );
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output,
    ): int
    {
        $usersNumber = $input->getOption('users-number');
        $postsNumber = $input->getOption('posts-number');
        $commentsNumber = $input->getOption('comments-number');
        $users = [];
        $posts = [];
        if (empty($usersNumber) || (int)$usersNumber < 1) {
            $output->writeln('No users to create');
            return Command::SUCCESS;
        }
        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }
        if (empty($postsNumber) || (int)$postsNumber < 1) {
            $output->writeln('No posts to create');
            return Command::SUCCESS;
        }
        foreach ($users as $user) {
            for ($i = 0; $i < $postsNumber; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->title());
            }
        }
        if (empty($commentsNumber) || (int)$commentsNumber < 1) {
            $output->writeln('No comments to create');
            return Command::SUCCESS;
        }
        foreach ($posts as $post) {
            for ($i = 0; $i < $commentsNumber; $i++) {
                $user = array_rand($users, 1);
                $comment = $this->createFakeComment($post, $users[$user]);
                $output->writeln('Comment created: ' . $post->title());
            }
        }
        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            $this->faker->password,
            new Name(
                $this->faker->firstName,
                $this->faker->lastName
            )
        );

        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(Post $post, User $user): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $user,
            $post,
            $this->faker->realText
        );

        $this->commentsRepository->save($comment);
        return $comment;
    }
}