<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\TraceableTagAwareAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    /* Get all products */
    #[Route('/api/products', name: 'app_products', methods: ['GET'])]
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
            return $serializer->serialize($products, 'json', ['groups' => 'getProducts']);
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
    #[Route('/api/products/{id}', name: 'app_product', methods: ['GET'])]
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
