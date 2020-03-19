<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

abstract class AbstractBaseFixtures extends Fixture
{
    protected const USER_COUNT = 500;
    protected const POST_COUNT = 5000;
    protected const COMMENT_COUNT = 50000;
    protected const TAG_COUNT = 100;

    /** @var ObjectManager */
    protected $manager;

    /** @var \Faker\Generator  */
    protected $faker;

    abstract protected function loadData();

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker   = Factory::create();

        $this->loadData();
    }

    protected function createMany($className, $count, callable $callable)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className;
            $callable($entity);

            $this->manager->persist($entity);
            $this->addReference($className.'_'.$i, $entity);
        }
    }
}