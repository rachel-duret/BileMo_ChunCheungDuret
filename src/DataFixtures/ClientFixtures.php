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
        $client->setRoles(["ROLE_USER"]);
        $client->setCreatedAt(new DateTimeImmutable());
        $manager->persist($client);

        $client1 = new Client();

        $client1->setClientName('admin');
        $client1->setEmail('admin@gmail.com');
        $client1->setPassword($this->userPasswordHasher->hashPassword($client1, "password"));
        $client1->setRoles(["ROLE_ADMIN"]);
        $client1->setCreatedAt(new DateTimeImmutable());
        $manager->persist($client1);

        $manager->flush();

        $this->addReference('client', $client);
    }
}
