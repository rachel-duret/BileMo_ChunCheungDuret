<?php

namespace App\Service;

use App\Repository\ClientRepository;
use DateTimeImmutable;

class UserService
{

    public function addOneUser($user, $request, $clientRepository, $em): void
    {
        $user->setCreatedAt(new DateTimeImmutable());
        $content = $request->toArray();
        $clientId = $content['clientId'];
        $user->setClient($clientRepository->find($clientId));
        $em->persist($user);
        $em->flush();
    }
}
