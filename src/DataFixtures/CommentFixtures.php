<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    protected function loadData()
    {
        $this->createMany(Comment::class, 100, function (Comment $comment) {
            $comment->setMessage($this->faker->text(140));
            $datetime = $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s');
            $comment->setCreatedAt(new \DateTime($datetime));
            $comment->setAuthor($this->getReference(User::class.'_'.rand(0, 24)));
            $comment->setPost($this->getReference(Post::class.'_'.rand(0, 49)));
        });
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}