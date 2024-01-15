<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\GroupRepository;

class GroupService
{
    public function __construct(
        private readonly GroupRepository $groupRepository,
    ){}

    public function setGroupData(Group $user, array $groupDTO): Group
    {
        if (isset($groupDTO['name'])) {
            $user->setName($groupDTO['name']);
        }

        return $user;
    }
}
