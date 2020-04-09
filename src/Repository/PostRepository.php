<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use App\Exception\ItemNotFoundException;
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
                ->orderBy('p.createdAt', 'DESC')
                ->getQuery()
                ->execute();
    }

    /**
     * @param array|string $tags
     * @return mixed
     */
    public function findPostsByTags($tags)
    {
        return $this->createQueryBuilder('p')
            ->join('p.tags', 'tags')
            ->where('tags.title IN (:tags)')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->execute();
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $post = parent::findOneBy($criteria, $orderBy);

        return $post;
    }



//    public function findPostsId()
//    {
//        return $this->createQueryBuilder('p')
//            ->select('p.id')
//            ->where('p.id >= :min')
//            ->andWhere('p.id < :max')
//            ->setParameters([
//                'min' => 1,
//                'max' => 100,
//            ])
////                ->orderBy('p.createdAt', 'DESC')
//            ->getQuery()
//            ->execute();
//    }
}