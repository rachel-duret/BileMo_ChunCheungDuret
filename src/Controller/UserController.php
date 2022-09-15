<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /* Add one user */
    #[Route('/api/users', name: 'addOneUser', methods: ['POST'])]
    public function addOneUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ClientRepository $clientRepository
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $content = $request->toArray();
        $clientId = $content['clientId'];
        $user->setClient($clientRepository->find($clientId));
        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
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
    public function getOneUser(User $user, SerializerInterface $serializer)
    {
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUsers']);
        return new JsonResponse(
            $jsonUser,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /* Get all user */
    #[Route('/api/users', name: 'getAllUsers', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->findAll();
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);

        return new JsonResponse(
            $jsonUserList,
            Response::HTTP_OK,
            [],
            true
        );
    }


    /* Delete one user */

    #[Route('/api/users/{id}', name: 'deleteOneUser', methods: ['DELETE'])]
    public function deleteOneBook(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
