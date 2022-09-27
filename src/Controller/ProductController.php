<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Service\CacheService;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;


class ProductController extends AbstractController
{
    /* Get all products */
    #[Route('/api/products', name: 'getAllProducts', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns list of products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['getProducts']))
        )
    )]
    #[OA\Response(response: 403, description: 'Access forbidden',)]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Return number of page',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'Return product of limit',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Product')]
    //#[Security(name: 'Bearer')]
    public function getAllProduct(
        ProductRepository $productRepository,
        Request $request,
        CacheService $cacheService,
        ClientRepository $clientRepository
    ): JsonResponse {
        $user = $clientRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        if ($user) {
            $getGroups = "getProducts";
            $productsCache = "productscache";
            //Call cache serrver
            $jsonProducts = $cacheService->cache($request, $productRepository, $getGroups, $productsCache);
            return new JsonResponse(
                $jsonProducts,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_FORBIDDEN
        );
    }

    /* Get one product */
    #[Route('/api/products/{id}', name: 'getOneProduct', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return detail of product by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['getProducts']))
        )
    )]
    #[OA\Response(response: 403, description: 'Access forbidden',)]
    #[OA\Tag(name: 'Product')]
    //#[Security(name: 'Bearer')]
    public function getOneProduct(
        Product $product,
        SerializerInterface $serializer,
        ClientRepository $clientRepository
    ) {
        $user = $clientRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        if ($user) {
            $context = SerializationContext::create()->setGroups(["getProducts"]);
            $jsonProduct = $serializer->serialize($product, 'json', $context);
            return new JsonResponse(
                $jsonProduct,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_FORBIDDEN
        );
    }
}
