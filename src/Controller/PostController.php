<?php

namespace App\Controller;

use App\Dto\CreatePostDto;
use App\Dto\CreateUserDto;
use App\Entity\Post;
use App\EventListener\RequestListener;
use App\Service\NotificationEmailService;
use App\Service\PostService;
use App\Service\RequestService;
use App\Service\ResponseService;
use App\Utils\BaseController;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends BaseController
{
    /** @var RequestService  */
    private $requestService;

    /** @var PostService  */
    private $postService;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(
        RequestService $requestService,
        PostService $postService,
        ResponseService $responseService,
        LoggerInterface $logger
    )
    {
        $this->requestService = $requestService;
        $this->postService = $postService;
        $this->logger = $logger;

        parent::__construct($responseService);
    }

    /**
     *
     * @Route("/api/posts", name="posts_list", methods={"GET"})
     */
    public function list()
    {
        $params = $this->requestService->getParameters();
        $posts = $this->postService->getPosts($params);

        return $this->buildResponse($posts, 200);
    }

    /**
     * @Route("/api/posts/{id}", name="post_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Post $post)
    {
        return $this->buildResponse($post, 200);
    }

    /**
     * @Route("/api/posts", name="post_create", methods={"POST"})
     * @ParamConverter()
     */
    public function create(CreatePostDto $requestDto, Request $request)
    {
        $this->logger->info('Wow. I`am in '.__METHOD__.' now. Great!');
        $post = $this->postService->createNewPost(
            $requestDto->getTitle(),
            $requestDto->getText(),
            $requestDto->getAuthorId()
        );

        return $this->buildResponse($post, 201);
    }

    /**
     * @Route("/api/posts/{id}", name="post_edit", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, Request $request)
    {
        $post = $this->postService->getPost($id);
        $post = $this->postService->editPost($post, $request);

        return $this->buildResponse($post, 203);
    }

    /**
     * @Route("/api/posts/{id}", name="post_destroy", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function destroy(int $id)
    {
        $post = $this->postService->getPost($id);
        $this->postService->deletePost($post);

        return $this->buildResponse('', 204);
    }
}