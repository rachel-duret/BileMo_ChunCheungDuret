<?php

namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;

interface GenericPaginationServiceInterface
{
    public function findAllWithPagination(int $offset, int $limt, ?UserInterface $client = null);

    public function countAll(?UserInterface $client = null);
}
