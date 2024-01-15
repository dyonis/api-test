<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Service\GroupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    public function __construct(
        private readonly GroupRepository $groupRepository,
        private readonly GroupService $groupService,
    ){}

    #[Route('/group/{id}', name: 'get_group', methods: ['GET'])]
    public function index(int $id): JsonResponse
    {
        $group = $this->getGroup($id);

        return new JsonResponse($group);
    }

    #[Route('/groups', name: 'get_groups', methods: ['GET'])]
    public function getGroups(): JsonResponse
    {
        $groups = $this->groupRepository->findAll();

        return $this->json($groups);
    }

    #[Route('/group', name: 'create_group', methods: ['POST'])]
    public function createGroup(Request $request): JsonResponse
    {
        try {
            $data = $this->getGroupDataFromRequest($request);
            $group = new Group();
            $this->groupService->setGroupData($group, $data);
            $this->groupRepository->save($group);

            return $this->json(['message' => 'Group created successfully'], Response::HTTP_CREATED);
        } catch (\Exception) {
            // todo: log error
            return $this->json(['message' => 'Internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/group/{id}', name: 'update_group', methods: ['PUT', 'PATCH'])]
    public function updateGroup(int $id, Request $request): JsonResponse
    {
        try {
            $data = $this->getGroupDataFromRequest($request);
            $group = $this->getGroup($id);
            $this->groupService->setGroupData($group, $data);
            $this->groupRepository->save($group);

            return $this->json(['message' => 'Group updated successfully']);
        } catch (\Exception) {
            // todo: log error
            return $this->json(['message' => 'Internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/group/{id}', name: 'delete_group', methods: ['DELETE'])]
    public function deleteGroup(int $id): JsonResponse
    {
        try {
            $group = $this->getGroup($id);
            $this->groupRepository->remove($group);

            return $this->json(['message' => 'Group deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (NotFoundHttpException) {
            return $this->json(['message' => 'Group deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception) {
            // todo: log error
            return $this->json(['message' => 'Internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/group/{groupId}/add-user/{userId}', name: 'add_user_to_group', methods: ['DELETE'])]
    public function addUser(int $groupId, int $userId): JsonResponse
    {
        // todo: implement
        return new JsonResponse('Not implemented');
    }

    private function getGroup(int $id): Group
    {
        if (!$group = $this->groupRepository->getOneById($id)) {
            throw new NotFoundHttpException('Group not found');
        }

        return $group;
    }

    private function getGroupDataFromRequest(Request $request): array
    {
        if (!$data = json_decode($request->getContent(), true)) {
            throw new BadRequestHttpException('Invalid data provided');
        }

        return $data;
    }
}
