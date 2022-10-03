<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }


    public function addOneUser(User $user, Client $loggerUser): void
    {
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setClient($loggerUser);
        $this->em->persist($user);
        $this->em->flush();
    }
}
