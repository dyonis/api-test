<?php

namespace App\Transformer;

use App\Entity\Group;

// todo: use https://symfony.com/doc/current/serializer.html#using-serialization-groups-attributes
// $jsonContent = $this->serializer->serialize($users, 'json', ['groups' => 'users']);
class GroupTransformer
{
    /**
     * @param array<int, Group> $groups
     * @return array
     */
    public function manyToArray(array $groups): array
    {
        $data = [];

        foreach ($groups as $user) {
            $data[] = $this->oneToArray($user);
        }

        return $data;
    }

    public function oneToArray(Group $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
        ];
    }
}
