<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'app_products', methods:['GET'])]
    public function getAllProduct(): JsonResponse
    {
        return new JsonResponse([
            
        ]);
    }
}
