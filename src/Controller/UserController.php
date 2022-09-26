<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheService;
use App\Service\UserService;
use App\Service\VersioningService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserController extends AbstractController
{
    /* Add one user */
    #[Route('/api/users', name: 'addOneUser', methods: ['POST'])]
    #[IsGranted("ROLE_USER", message: 'You do not have the right to add one user.')]
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
    public function getAllUsers(
        UserRepository $userRepository,
        Request $request,
        CacheService $cacheService
    ): JsonResponse {

        $getGroups = "getUsers";
        $userCache = "usersCache";
        $clientEmail = $this->getUser()->getUserIdentifier();


        //call cache service
        $jsonUserList = $cacheService->cache($request, $userRepository, $getGroups, $userCache, $clientEmail);

        return new JsonResponse(
            $jsonUserList,
            Response::HTTP_OK,
            [],
            true
        );
    }


    /* Delete one user */

    #[Route('/api/users/{id}', name: 'deleteOneUser', methods: ['DELETE'])]
    public function deleteOneBook(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $cachePool->invalidateTags(["usersCache"]);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
