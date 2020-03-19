<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;

class TagFixtures extends AbstractBaseFixtures
{
    protected function loadData()
    {
        $this->createMany(Tag::class, self::TAG_COUNT, function (Tag $tag) {
            $tag->setTitle($this->faker->word());
            $tag->addPost($this->getReference(Post::class.'_'.rand(0, self::POST_COUNT - 1)));
        });

        $this->manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            PostFixtures::class,
        ];
    }
}