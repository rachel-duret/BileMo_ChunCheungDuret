<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        for ($i = 0; $i < 20; $i++) {

            $user->setUsername('username' . $i);
            $user->setEmail("username$i@gmail.com");
            $user->setClient($this->getReference('client'));
            $user->setCreatedAt(new DateTimeImmutable());
            $manager->flush();
        }
    }
}
