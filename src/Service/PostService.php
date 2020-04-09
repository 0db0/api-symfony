<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Event\PostCreatedEvent;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    /** @var TagRepository  */
    private $tagRepository;

    /** @var UserService  */
    private $userService;

    /** @var EventDispatcherInterface  */
    private $dispatcher;

    public function __construct(PostRepository $repository,
                                EntityManagerInterface $em,
                                TagRepository $tagRepository,
                                UserService $userService,
                                EventDispatcherInterface $dispatcher
    )
    {
        $this->repository     = $repository;
        $this->cache          = new RedisAdapter(new Client());
        $this->em             = $em;
        $this->tagRepository  = $tagRepository;
        $this->userService    = $userService;
        $this->dispatcher     = $dispatcher;
    }

    /**
     * @param array $params
     * @return array| Post[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPosts(array $params): array
    {
        list(
            'limit' =>$limit,
            'offset' => $offset,
            'tags' => $tags
            ) = $params;

        if (!empty($tags)) {
            return $this->repository->findPostsByTags($tags);
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

    public function getPost(int $id): ?Post
    {
        return $this->repository->findOneBy(['id' => $id]);

    }

    public function createNewPost(string $title, string $text, int $authorId): Post
    {
        $user = $this->userService->getUserById($authorId);
        $post = new Post($title, $text, $user);
        $this->save($post);

//        $this->logger->debug('post created', [
//            'post_id' => $post->getId()
//        ]);

        $this->dispatcher->dispatch(new PostCreatedEvent($post), PostCreatedEvent::NAME);

        return $post;
    }

    public function editPost(Post $post, Request $request): Post
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

    public function getPostAuthor(Post $post): User
    {
        return $post->getAuthor();
    }

    private function save(Post $post): void
    {
        $this->em->persist($post);
        $this->em->flush();
    }
}