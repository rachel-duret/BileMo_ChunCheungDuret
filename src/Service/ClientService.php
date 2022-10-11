<?php

namespace App\Service;

use App\Repository\ClientRepository;

class ClientService
{
    public function __construct(private ClientRepository $clientRepository)
    {
    }

    public function getOneClient(string $email)
    {
        return  $this->clientRepository->findOneBy(['email' => $email]);
    }
}
