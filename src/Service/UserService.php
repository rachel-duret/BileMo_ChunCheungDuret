<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService implements GenericPaginationServiceInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository
    ) {
    }

    public function findAllWithPagination(int $offset, int $limt, ?UserInterface $client = null)
    {
        return $this->userRepository->findAllWithPagination($offset, $limt, $client);
    }
    public function countAll(?UserInterface $client = null)
    {
        return $this->userRepository->countAll($client);
    }


    public function addOneUser(User $user, Client $loggerUser): void
    {
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setClient($loggerUser);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(object $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findOneBy(string $username)
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    public function find(int $id)
    {
        return $this->userRepository->find($id);
    }
}
