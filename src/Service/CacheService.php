<?php

namespace App\Service;


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
        GenericPaginationServiceInterface $service,
        string $route,
        UserInterface $client = null
    ) {
        $page = (int) $request->get(key: 'page', default: 1);
        $limit = (int) $request->get(key: 'limit', default: 3);
        // Cache setting
        $cacheId = "$route-$page-$limit";
        $userList = $this->cachePool->get($cacheId, function (ItemInterface $item) use ($service, $page, $limit,  $route, $client) {
            $item->tag($route . 'Cache');
            $item->expiresAfter(3);

            //pagination users
            return $this->paginationService->paginate($page, $limit, $route, $service, $client);
        });
        // dd($userList);
        return   $userList;
    }
}
