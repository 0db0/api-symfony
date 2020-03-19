<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * By default method returns 100 posts, ordered by DESC
     *
     * @param int $limit
     * @param int $offset
     * @return Post[]
     */
    public function findPosts(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
                ->where('p.id >= :min')
                ->andWhere('p.id < :max')
                ->setParameters([
                    'min' => $offset,
                    'max' => $offset + $limit,
                ])
//                ->orderBy('p.createdAt', 'DESC')
                ->getQuery()
                ->execute();
    }
}