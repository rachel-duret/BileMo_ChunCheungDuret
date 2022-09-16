<?php

namespace App\DataFixtures;

use App\Entity\Client;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture
{
    private $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $client = new Client();

        $client->setClientName('smart mobile');
        $client->setEmail('contact@smartmobile.com');
        $client->setPassword($this->userPasswordHasher->hashPassword($client, "password"));
        $client->setCreatedAt(new DateTimeImmutable());
        $manager->persist($client);

        $manager->flush();

        $this->addReference('client', $client);
    }
}
