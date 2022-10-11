<?php

namespace App\Service;

use App\Repository\RepositoryWithPagination;
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


    public function __construct(private TagAwareCacheInterface $cachePool, private SerializerInterface $serializer)
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
            //pagination users
            $offset = ($page - 1) * $limit;
            $totalItems = $repository->countAll($client);
            $list = $repository->findAllWithPagination($offset, $limit, $client);

            $pages = (int) ceil($totalItems[1] / intval($limit));

            $collection =   new CollectionRepresentation($list, $offset, $limit);
            $paginatedCollection = new PaginatedRepresentation(
                $collection,
                $route, // route
                array(), // route parameters
                $page,       // page number
                $limit,      // limit
                $pages,
            );

            $context = SerializationContext::create()->setGroups(["Default"]);
            return $this->serializer->serialize($paginatedCollection, 'json', $context);
        });

        return   $jsonUserList;
    }
}
