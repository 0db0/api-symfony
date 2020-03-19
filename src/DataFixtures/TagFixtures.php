<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TagFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    private const TAGS = ['php', 'mozilla', 'v8', 'symfony', 'laravel', 'go', 'TDD', 'highload'];

    protected function loadData()
    {
        $this->createMany(Tag::class, self::TAG_COUNT, function (Tag $tag) {
            if (rand(0, 100) > 50) {
                try {
                    $tag->setTitle($this->faker->unique()->randomElement(self::TAGS));
                } catch (\OverflowException $e) {
                    $tag->setTitle($this->faker->unique()->word());
                }
            } else {
                $tag->setTitle($this->faker->unique()->word());
            }
//            $tag->addPost($this->getReference(Post::class.'_'.rand(0, self::POST_COUNT - 1)));
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
//            PostFixtures::class,
        ];
    }
}