<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CacheService;
use App\Service\UserService;
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
use Symfony\Component\Validator\Constraints as Assert;


class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }
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
    #[OA\Response(response: 409, description: 'User already exist .',)]
    #[OA\RequestBody(
        required: true,
        description: 'Add one user',
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['addUser']))

    )]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function addOneUser(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        UserService $userService,
    ): JsonResponse {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        //Check data before stock in the database
        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse(
                data: $this->serializer->serialize($errors, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true
            );
        }
        // check user already exist or not
        if (!empty($this->userRepository->findOneBy(['username' => $user->getUsername()]))) {
            return new JsonResponse(
                data: ['Message' => 'User already exist .'],
                status: Response::HTTP_CONFLICT
            );
        }
        // call user service setting user befor insert to database;
        $userService->addOneUser($user, $this->getUser());
        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $this->serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate('getOneUser', ['id' => $user->getId()]);
        return new JsonResponse(
            data: $jsonUser,
            status: Response::HTTP_CREATED,
            headers: ["Location" => $location],
            json: true
        );
    }

    /* ********************************* Get one user******************************** */
    #[Route('api/users/{id}', name: 'getOneUser', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return the detail of user by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Response(response: 403, description: 'Logged user do not have the right to access ',)]
    #[OA\Response(response: 404, description: 'User not found  ',)]
    #[OA\Tag(name: 'User')]
    #[Security(name: 'Bearer')]
    public function getOneUser(int $id)
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            return new JsonResponse(
                data: ['Message' => 'User not found.'],
                status: Response::HTTP_NOT_FOUND
            );
        }
        // check logged user is same as user.client
        if ($user->getClient() !== $this->getUser()) {
            return new JsonResponse(
                data: ['Message' => 'You do not have the right to access this user'],
                status: Response::HTTP_FORBIDDEN
            );
        }

        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $this->serializer->serialize($user, 'json', $context);
        return new JsonResponse(
            data: $jsonUser,
            status: Response::HTTP_OK,
            json: true
        );
    }

    /************************************* Get all user ****************************************** */
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
    #[Security(name: 'Bearer')]
    public function getAllUsers(
        Request $request,
        CacheService $cacheService,
    ): JsonResponse {

        $constraints =  new Assert\Collection([
            "page" => [new Assert\Type(['type' => 'numeric'])],
            "limit" => [new Assert\Type(['type' => 'numeric'])]
        ]);
        $errors = $this->validator->validate($request->query->all(), $constraints);
        if ($errors->count() > 0) {
            return new JsonResponse(
                data: $this->serializer->serialize($errors, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true
            );
        }

        $route = "getAllUsers";
        //call cache service
        $jsonUserList = $cacheService->cache(
            $request,
            $this->userRepository,
            $route,
            $this->getUser()
        );
        //dd($jsonUserList);
        return new JsonResponse(
            data: $jsonUserList,
            status: Response::HTTP_OK,
            json: true
        );
    }

    /*************************  Delete one user*****************************88888 */

    #[Route('/api/users/{id}', name: 'deleteOneUser', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Success user delete, no content return',
    )]
    #[OA\Response(response: 403, description: 'Logged user do not have the right  ',)]
    #[OA\Response(response: 404, description: 'User not found  ',)]
    #[OA\Tag(name: 'User')]
    //#[Security(name: 'Bearer')]
    public function deleteOneUser(int $id, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            return new JsonResponse(
                data: ['Message' => 'User not found.'],
                status: Response::HTTP_NOT_FOUND
            );
        }

        if ($user->getClient() !== $this->getUser()) {
            return new JsonResponse(
                data: ['Message' => 'You do not have the right to delete this user !',],
                status: Response::HTTP_FORBIDDEN
            );
        }
        $cachePool->invalidateTags(["getAllUsersCache"]);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
