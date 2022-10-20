<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CacheService;
use App\Service\ClientService;
use App\Service\ProductService;
use App\Validators\RequestValidator;
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
    public function __construct(
        private ClientService $clientService,
        private SerializerInterface $serializer,
        private ProductService $productService,
        private RequestValidator $requestValidator,
    ) {
    }
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
    #[OA\Response(response: 400, description: 'Bad request .',)]
    #[OA\Response(response: 404, description: 'Page not found',)]
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
    #[Security(name: 'Bearer')]
    public function getAllProduct(
        Request $request,
        CacheService $cacheService,
    ): JsonResponse {
        // check request 
        $errors = $this->requestValidator->validate($request);
        if ($errors) {
            return new JsonResponse(
                data: $this->serializer->serialize($errors, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true
            );
        }

        // check logged user is the client of BileMo
        $client = $this->clientService->getOneClient($this->getUser()->getUserIdentifier());
        if (!$client) {
            /*
            if logged user is not client of BileMo ,then retrun response 403 with a message, But for security we return 404 
            */
            return new JsonResponse(
                data: ['Message' => 'Page not found'],
                status: Response::HTTP_NOT_FOUND
            );
        }
        $route = "getAllProducts";
        //Call cache serrver
        $jsonProducts = $cacheService->cache(
            $request,
            $this->productService,
            $route
        );
        return new JsonResponse(
            data: $jsonProducts,
            status: Response::HTTP_OK,
            json: true
        );
    }

    /* **********************Get one product *******************************/
    #[Route('/api/products/{id}', name: 'getOneProduct', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return detail of product by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['getProducts']))
        )
    )]
    #[OA\Response(response: 400, description: 'Bad request .',)]
    #[OA\Response(response: 404, description: 'Product not found',)]
    #[OA\Tag(name: 'Product')]
    //#[Security(name: 'Bearer')]
    public function getOneProduct(
        SerializerInterface $serializer,
        Request $request,
    ) {
        // check request 
        $errors = $this->requestValidator->validate($request);
        if ($errors) {
            return new JsonResponse(
                data: $this->serializer->serialize($errors, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true
            );
        }
        $id = $request->get('id');
        // check logged user is the client of BileMo
        $client = $this->clientService->getOneClient($this->getUser()->getUserIdentifier());
        if (!$client) {
            // if logged user is not client of BileMo ,then retrun response 403 with a message
            return new JsonResponse(
                data: ['Message' => 'Page not found'],
                status: Response::HTTP_NOT_FOUND
            );
        }

        $product = $this->productService->find($id);
        if (empty($product)) {
            return new JsonResponse(
                data: ['Message' => 'Product not found.'],
                status: Response::HTTP_NOT_FOUND
            );
        }

        $context = SerializationContext::create()->setGroups(["getProducts"]);
        $jsonProduct = $serializer->serialize($product, 'json', $context);
        return new JsonResponse(
            data: $jsonProduct,
            status: Response::HTTP_OK,
            json: true
        );
    }
}
