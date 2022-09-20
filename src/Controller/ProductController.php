<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    /* Get all products */
    #[Route('/api/products', name: 'getAllProducts', methods: ['GET'])]
    public function getAllProduct(
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {
        //create paginarion by default 1page 3products
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        // Cache setting
        $cacheId = "getAllProducts-$page-$limit";
        $jsonProducts = $cachePool->get($cacheId, function (ItemInterface $item) use ($productRepository, $page, $limit, $serializer) {
            // echo is for test teh cache, will delete it in production
            echo ("not cache yet");
            $item->tag("productsCache");
            //pagination prroducts
            $products = $productRepository->findAllWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(["getProducts"]);
            return $serializer->serialize($products, 'json', $context);
        });

        return new JsonResponse(
            $jsonProducts,
            Response::HTTP_OK,
            [],
            true
        );
        //pagenation
    }

    /* Get one product */
    #[Route('/api/products/{id}', name: 'getOneProduct', methods: ['GET'])]
    public function getOneProduct(Product $product, SerializerInterface $serializer)
    {
        $context = SerializationContext::create()->setGroups(["getProducts"]);
        $jsonProduct = $serializer->serialize($product, 'json', $context);
        return new JsonResponse(
            $jsonProduct,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
