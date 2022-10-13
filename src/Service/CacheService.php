<?php

namespace App\Service;


use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CacheService
{


    public function __construct(private TagAwareCacheInterface $cachePool, private PaginationService $paginationService)
    {
    }

    public function cache(
        Request $request,
        object $repository,
        string $route,
        UserInterface $client = null
    ) {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        // Cache setting
        $cacheId = "$route-$page-$limit";
        $jsonUserList = $this->cachePool->get($cacheId, function (ItemInterface $item) use ($repository, $page, $limit,  $route, $client) {
            $item->tag($route . 'Cache');
            $item->expiresAfter(3);

            //pagination users
            return $this->paginationService->paginate($page, $limit, $route, $repository, $client);
        });

        return   $jsonUserList;
    }
}
