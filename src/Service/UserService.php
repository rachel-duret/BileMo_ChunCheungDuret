<?php

namespace App\Service;

use App\Entity\Client;
use DateTimeImmutable;

class UserService
{


    public function addOneUser($user, Client $loggerUser): void
    {
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setClient($loggerUser);
    }
}
