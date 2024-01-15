<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\GroupRepository;

class UserService
{
    public function __construct(
        private readonly GroupRepository $groupRepository,
    ){}

    public function setUserData(User $user, array $userDTO): User
    {
        if (isset($userDTO['name'])) {
            $user->setName($userDTO['name']);
        }

        if (isset($userDTO['email'])) {
            $user->setEmail($userDTO['email']);
        }

        if ($groupIds = $userDTO['groups'] ?? []) {
            $groups = $this->groupRepository->findByIds($groupIds);

            foreach ($groups as $group) {
                $user->addGroup($group);
            }

            // todo: throw an exception if not all groups were found
        }

        return $user;
    }
}
