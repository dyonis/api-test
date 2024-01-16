<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Transformer\UserTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GroupRepository $groupRepository,
        private readonly UserService $userService,
        private readonly UserTransformer $userTransformer,
    ){}

    #[Route('/user/{id}', name: 'get_user', methods: ['GET'])]
    public function index(int $id): JsonResponse
    {
        $user = $this->getApiUser($id);

        return new JsonResponse($this->userTransformer->oneToArray($user));
    }

    #[Route('/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        return $this->json($this->userTransformer->manyToArray($users));
    }

    #[Route('/user', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        try {
            $data = $this->getUserDataFromRequest($request);
            $user = new User();
            $this->userService->setUserData($user, $data);
            $this->userRepository->save($user);

            return $this->json(
                $this->userTransformer->oneToArray($user),
                Response::HTTP_CREATED,
            );
        } catch (\Exception) {
            // todo: log error
            return $this->json(['message' => 'Internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/user/{id}', name: 'update_user', methods: ['PUT', 'PATCH'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        try {
            $data = $this->getUserDataFromRequest($request);
            $user = $this->getApiUser($id);
            $this->userService->setUserData($user, $data);
            $this->userRepository->save($user);

            return $this->json($this->userTransformer->oneToArray($user));
        } catch (\Exception) {
            // todo: log error
            return $this->json(['message' => 'Internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/user/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $user = $this->getApiUser($id);
            $this->userRepository->remove($user);

            return $this->json(['message' => 'User deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (NotFoundHttpException) {
            return $this->json(['message' => 'User deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception) {
            // todo: log error
            return $this->json(['message' => 'Internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/user/{userId}/add-to-group/{groupId}', name: 'user_add_to_group', methods: ['PUT', 'PATCH'])]
    public function addToGroup(int $userId, int $groupId): JsonResponse
    {
        try {
            $user = $this->getApiUser($userId);
            $group = $this->getGroup($groupId);

            $user->addGroup($group);
            $this->userRepository->save($user);

            return $this->json($this->userTransformer->oneToArray($user));
        } catch (\Exception $e) {
            return $this->json(
                ['message' => 'Internal error: '.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/user/{userId}/remove-from-group/{groupId}', name: 'user_remove_from_group')]//, methods: ['PUT', 'PATCH'])]
    public function removeFromGroup(int $userId, int $groupId): JsonResponse
    {
        try {
            $user = $this->getApiUser($userId);
            $group = $this->getGroup($groupId);

            $user->removeGroup($group);
            $this->userRepository->save($user);

            return $this->json($this->userTransformer->oneToArray($user));
        } catch (\Exception $e) {
            return $this->json(
                ['message' => 'Internal error: '.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function getApiUser(int $id): User
    {
        if (!$user = $this->userRepository->getOneById($id)) {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    private function getUserDataFromRequest(Request $request): array
    {
        if (!$data = json_decode($request->getContent(), true)) {
            throw new BadRequestHttpException('Invalid data provided');
        }

        return $data;
    }

    private function getGroup(int $id): Group
    {
        if (!$group = $this->groupRepository->getOneById($id)) {
            throw new NotFoundHttpException('Group not found');
        }

        return $group;
    }
}
