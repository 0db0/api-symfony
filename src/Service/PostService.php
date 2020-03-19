<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;

class PostService
{
    private const CACHE_EXPIRES_AFTER = 60;

    /** @var PostRepository  */
    private $repository;

    /** @var RedisAdapter  */
    private $cache;

    /** @var EntityManagerInterface  */
    private $em;

    /** @var UserRepository  */
    private $userRepository;

    /** @var TagRepository  */
    private $tagRepository;

    public function __construct(PostRepository $repository,
                                EntityManagerInterface $em,
                                UserRepository $userRepository,
                                TagRepository $tagRepository
    )
    {
        $this->repository = $repository;
        $this->cache = new RedisAdapter(new Client());
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
    }

    public function getPosts(int $limit, int $offset, string $tags): array
    {
        if ($tags) {
            return $this->tagRepository->findPostByTags($tags);
        }

        $key = sprintf(
            'posts_list_%d_%d',
                    $offset,
                    $limit
        );

        $posts = $this->cache->get($key, function (ItemInterface $item) use ($limit, $offset) {
            $item->expiresAfter(self::CACHE_EXPIRES_AFTER);
            $posts = $this->repository->findPosts($limit, $offset);

            return serialize($posts);
        });

        return unserialize($posts);
    }

    public function findPost(int $id): Post
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function createNewPost($item): Post
    {
        $post = new Post();
        $post->setTitle($item->title);
        $post->setText($item->text);
        $user = $this->getUserById($item->author);
        $post->setAuthor($user);
        $this->save($post);

        return $post;
    }

    public function editPost(Post $post, Request $request)
    {
        $content = json_decode($request->getContent());

        if (property_exists($content, 'title')) {
            $post->setTitle($content->title);
        }

        if (property_exists($content, 'text')) {
            $post->setText($content->text);
        }

        $post->setUpdatedAt(new \DateTime());

        $this->save($post);

        return $post;
    }

    public function deletePost(Post $post): void
    {
        $this->em->remove($post);
        $this->em->flush();
    }

    private function getUserById(int $id): User
    {
        return $this->userRepository->findOneBy(['id' => $id]);
    }

    private function save(Post $post)
    {
        $this->em->persist($post);
        $this->em->flush();
    }
}