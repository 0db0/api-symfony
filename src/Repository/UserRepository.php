<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllUsers()
    {
        return $this->createQueryBuilder('u')
                   ->setMaxResults(self::LIMIT_MAX)
                   ->getQuery()
                   ->execute();
    }

    public function countAllUsers(): int
    {
        return $this->createQueryBuilder('u')
                         ->select('COUNT(u)')
                         ->getQuery()
                         ->execute(null, AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function findUsersByRange($offset, $limit)
    {
        return $this->createQueryBuilder('u')
                   ->where('u.id > :minValue')
                   ->andWhere('u.id <= :maxValue')
                   ->setParameters([
                       'minValue' => $offset,
                       'maxValue' => $limit,
                   ])
                   ->getQuery()
                   ->execute();
    }
}