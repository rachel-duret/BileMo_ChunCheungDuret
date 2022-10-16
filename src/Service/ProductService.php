<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Product;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductService implements GenericPaginationServiceInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository
    ) {
    }

    public function findAllWithPagination(int $offset, int $limt, ?UserInterface $client = null)
    {
        return $this->productRepository->findAllWithPagination($offset, $limt, $client);
    }
    public function countAll(?UserInterface $client = null)
    {
        return $this->productRepository->countAll($client);
    }

    public function find(int $id)
    {
        return $this->productRepository->find($id);
    }
}
