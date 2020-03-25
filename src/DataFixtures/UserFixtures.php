<?php

namespace App\DataFixtures;

use App\Entity\User;

class UserFixtures extends AbstractBaseFixtures
{
    const PLAIN_PASSWORD = 'happy_api';

    protected function loadData()
    {
        $this->createMany(User::class, self::USER_COUNT, function (User $user) {
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName);
            $user->setEmail($this->faker->email);
            $user->setPassword(self::PLAIN_PASSWORD);
//            $user->addFollower($this->getReference(User::class.'_'.rand(0, self::USER_COUNT)));
        });

        $this->manager->flush();
    }
}