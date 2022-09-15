<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    /* Get all products */
    #[Route('/api/products', name: 'app_products', methods: ['GET'])]
    public function getAllProduct(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $products = $productRepository->findAll();
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'getProducts']);
        return new JsonResponse(
            $jsonProducts,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /* Get one product */
    #[Route('api/products/{id}', name: 'app_product', methods: ['GET'])]
    public function getOneProduct(Product $product, SerializerInterface $serializer)
    {
        $jsonProduct = $serializer->serialize($product, 'json', ['groups' => 'getProducts']);
        return new JsonResponse(
            $jsonProduct,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
