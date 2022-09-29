<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\User;
use DateTimeImmutable;

class UserService
{


    public function addOneUser(User $user, Client $loggerUser): void
    {
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setClient($loggerUser);
    }
}
