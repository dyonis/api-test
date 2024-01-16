<?php

namespace App\Transformer;

use App\Entity\Group;
use App\Entity\User;

// todo: use https://symfony.com/doc/current/serializer.html#using-serialization-groups-attributes
// $jsonContent = $this->serializer->serialize($users, 'json', ['groups' => 'users']);
class UserTransformer
{
    /**
     * @param array<int, User> $users
     * @return array
     */
    public function manyToArray(array $users): array
    {
        $data = [];

        foreach ($users as $user) {
            $data[] = $this->oneToArray($user);
        }

        return $data;
    }

    public function oneToArray(User $user): array
    {
        $groupIds = array_map(function (Group $a){
            return $a->getId();
        }, $user->getGroups()->toArray());

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'groups' => $groupIds,
        ];
    }
}
