<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends AbstractBaseFixtures
{
    const PLAIN_PASSWORD = 'happy_api';


//    public function load(ObjectManager $manager)
//    {
//        $faker = Factory::create();
//        $user = new User();
//        $user->setFirstName($faker->firstName());
//        $user->setLastName($faker->lastName);
//        $user->setEmail($faker->email);
//        $user->setPassword(self::PLAIN_PASSWORD);
//
//        $manager->persist($user);
//        $manager->flush();
//    }

    protected function loadData()
    {
        $this->createMany(User::class, 25, function (User $user) {
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName);
            $user->setEmail($this->faker->email);
            $user->setPassword(self::PLAIN_PASSWORD);
        });

        $this->manager->flush();
    }
}