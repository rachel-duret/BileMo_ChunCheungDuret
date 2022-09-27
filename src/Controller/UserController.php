<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheService;
use App\Service\UserService;
use App\Service\VersioningService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;

class UserController extends AbstractController
{
    /* Add one user */
    #[Route('/api/users', name: 'addOneUser', methods: ['POST'])]
    #[IsGranted("ROLE_USER", message: 'You do not have the right to add one user.')]
    #[OA\Response(
        response: 201,
        description: 'Returns the new user just created',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\RequestBody(required: true,)]
    #[OA\Tag(name: 'User')]
    //#[Security(name: 'Bearer')]
    public function addOneUser(
        Request $request,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        UserService $userService,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        //Check data before stock in the database
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        // user setting befor insert to database;
        $userService->addOneUser($user, $this->getUser());
        $em->persist($user);
        $em->flush();

        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate('getOneUser', ['id' => $user->getId()]);
        return new JsonResponse(
            $jsonUser,
            Response::HTTP_CREATED,
            ["Location" => $location],
            true
        );
    }

    /*  Get one user */
    #[Route('api/users/{id}', name: 'getOneUser', methods: ['GET'])]
    #[Route('/api/users', name: 'addOneUser', methods: ['POST'])]
    #[IsGranted("ROLE_USER", message: 'You do not have the right to add one user.')]
    #[OA\Response(
        response: 200,
        description: 'Return the detail of user by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Tag(name: 'User')]
    //#[Security(name: 'Bearer')]
    public function getOneUser(
        User $user,
        SerializerInterface $serializer,
        VersioningService $versioningService
    ) {
        if ($user->getClient() === $this->getUser()) {
            $version = $versioningService->getVersion();
            $context = SerializationContext::create()->setGroups(["getUsers"]);
            $context->setVersion($version);
            $jsonUser = $serializer->serialize($user, 'json', $context);
            return new JsonResponse(
                $jsonUser,
                Response::HTTP_OK,
                [],
                true
            );
        }
        return new JsonResponse(null, Response::HTTP_FORBIDDEN);
    }

    /* Get all user */
    #[Route('/api/users', name: 'getAllUsers', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return list of users',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
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
    #[OA\Tag(name: 'User')]
    //#[Security(name: 'Bearer')]
    public function getAllUsers(
        UserRepository $userRepository,
        Request $request,
        CacheService $cacheService,
    ): JsonResponse {

        $getGroups = "getUsers";
        $userCache = "usersCache";
        $client  = $this->getUser();
        //call cache service
        $jsonUserList = $cacheService->cache($request, $userRepository, $getGroups, $userCache,  $client);


        return new JsonResponse(
            $jsonUserList,
            Response::HTTP_OK,
            [],
            true
        );
    }


    /* Delete one user */

    #[Route('/api/users/{id}', name: 'deleteOneUser', methods: ['DELETE'])]
    #[Route('/api/users', name: 'addOneUser', methods: ['POST'])]
    #[IsGranted("ROLE_USER", message: 'You do not have the right to add one user.')]
    #[OA\Response(
        response: 204,
        description: 'Success user delete, no content return',
    )]
    #[OA\Tag(name: 'User')]
    //#[Security(name: 'Bearer')]
    public function deleteOneBook(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        if ($user === $this->getUser()) {
            $cachePool->invalidateTags(["usersCache"]);
            $em->remove($user);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_FORBIDDEN);
    }
}
