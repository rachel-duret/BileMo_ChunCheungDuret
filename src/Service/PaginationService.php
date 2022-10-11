<?php

namespace App\Service;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PaginationService
{
    public function __construct(private SerializerInterface $serializer)
    {
    }
    public function paginate(string $page, string $limit, string $route, Object $repository, ?UserInterface $client = null)
    {
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
    }
}
