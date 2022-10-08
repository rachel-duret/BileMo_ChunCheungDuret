<?php

namespace App\Service;

use App\Repository\UserRepository;
use Hateoas\HateoasBuilder;
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
        string $getGroups,
        string $entityCache,
        string $route,
        UserInterface $client = null
    ) {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);


        // Cache setting
        $cacheId = "$getGroups-$page-$limit";
        $jsonUserList = $this->cachePool->get($cacheId, function (ItemInterface $item) use ($repository, $page, $limit,  $entityCache, $route, $client) {
            // echo is for test teh cache, will delete it in production
            echo ("not cache yet");
            $item->tag($entityCache);
            if ($route === "getAllUsers") {
                $list = $repository->findBy(['client' => $client]);
            } else {
                $list = $repository->findAll();
            }



            //pagination users
            $offset = ($page - 1) * $limit;
            $pages = (int) ceil(count($list) / $limit);

            $collection =   new CollectionRepresentation($list, $offset, $limit);
            $paginatedCollection = new PaginatedRepresentation(
                $collection,
                $route, // route
                array(), // route parameters
                $page,       // page number
                $limit,      // limit
                $pages,
            );

            //dd($paginatedCollection);

            $context = SerializationContext::create()->setGroups(["Default"]);

            // $hateoas = HateoasBuilder::create()->build();
            return $this->serializer->serialize($paginatedCollection, 'json', $context);
        });

        return   $jsonUserList;
    }
}
