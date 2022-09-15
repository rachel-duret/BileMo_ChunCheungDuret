<?php

namespace App\DataFixtures;

use App\Entity\Client;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = new Client();

        $client->setClientName('smart mobile');
        $client->setEmail('contact@smartmobile.com');
        $client->setPassword('password');
        $client->setCreatedAt(new DateTimeImmutable());
        $manager->persist($client);

        $manager->flush();

        $this->addReference('client', $client);
    }
}
