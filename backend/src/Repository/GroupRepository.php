<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function getOneById(int $id): ?Group
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array<int> $groupIds
     * @return array<Group>
     */
    public function findByIds(array $groupIds): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.id IN (:groupIds)')
            ->setParameter('groupIds', $groupIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $userId
     * @return array<int, Group>
     */
    public function getUserGroups(int $userId): array
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.users', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function save(Group $group)
    {
        $this->_em->persist($group);
        $this->_em->flush();
    }

    public function remove(Group $group)
    {
        $this->_em->remove($group);
        $this->_em->flush();
    }
}
