<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findPostByTags(string $tag)
    {
         return $this->createQueryBuilder('t')
             ->addSelect('p')
             ->join('t.post', 'p')
            ->where('t.title = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->execute();
    }
}