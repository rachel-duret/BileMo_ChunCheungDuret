<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CacheService
{
    private $cachePool;
    private $serializer;
    private $request;


    public function __construct(TagAwareCacheInterface $cachePool, SerializerInterface $serializer)
    {
        $this->cachePool = $cachePool;
        $this->serializer = $serializer;
    }

    public function cache($request, $repository, string $getGroups, string $entityCache, $client)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        // Cache setting
        $cacheId = "getAllProducts-$page-$limit";
        $jsonUserList = $this->cachePool->get($cacheId, function (ItemInterface $item) use ($repository, $page, $limit,  $getGroups, $entityCache, $client) {
            // echo is for test teh cache, will delete it in production
            echo ("not cache yet");
            $item->tag($entityCache);
            //pagination users

            $list = $repository->findAllWithPagination($page, $limit, $client);
            $context = SerializationContext::create()->setGroups([$getGroups]);
            return $this->serializer->serialize($list, 'json', $context);
        });
        return   $jsonUserList;
    }
}
