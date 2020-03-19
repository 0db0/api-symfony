<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    protected function loadData()
    {
        $this->createMany(Post::class, self::POST_COUNT, function (Post $post) {
           $post->setAuthor($this->getReference(User::class.'_'.rand(0, self::USER_COUNT - 1)));
           $post->setTitle($this->faker->sentence);
           $post->setText($this->faker->text);
           $post->addTags($this->getReference(Tag::class.'_'.rand(0, self::TAG_COUNT - 1)));
           $datetime = $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s');
           $post->setCreatedAt(new \DateTime($datetime));
        });

        $this->manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TagFixtures::class,
        ];
    }
}