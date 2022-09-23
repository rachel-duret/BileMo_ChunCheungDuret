<?php

namespace App\Service;

use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTimeImmutable;

class UserService
{

    public function addOneUser($user, $request,  $em, Client $loggerUser): void
    {
        $user->setCreatedAt(new DateTimeImmutable());
        $content = $request->toArray();
        $user->setClient($loggerUser);
        $em->persist($user);
        $em->flush();
    }
}
