<?php

namespace App\Controller;

use App\Service\PostService;
use App\Service\RequestService;
use App\Service\ResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /** @var RequestService  */
    private $requestService;

    /** @var PostService  */
    private $postService;

    /** @var ResponseService  */
    private $responseService;

    public function __construct(RequestService $requestService, PostService $postService, ResponseService $responseService)
    {
        $this->requestService = $requestService;
        $this->postService = $postService;
        $this->responseService = $responseService;
    }

    /**
     * @Route("/api/posts", name="posts_list", methods={"GET"})
     */
    public function list()
    {
        $params = $this->requestService->getParameters();
        $posts = $this->postService->getPosts($params);

        return $this->responseService->buildResponse($posts, 200);
    }

    /**
     * @Route("/api/posts/{id}", name="post_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(int $id)
    {
        $post = $this->postService->getPost($id);

        return $this->responseService->buildResponse($post, 200);
    }

    /**
     * @Route("/api/posts", name="post_create", methods={"POST"})
     */
    public function create()
    {
        $data = $this->requestService->getContent();
        $post = $this->postService->createNewPost($data);

        $this->postService->sendNotificationForFollowers($post);

        return $this->responseService->buildResponse($post, 201);
    }

    /**
     * @Route("/api/posts/{id}", name="post_edit", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, Request $request)
    {
        $post = $this->postService->findPost($id);
        $post = $this->postService->editPost($post, $request);

        return $this->responseService->buildResponse($post, 203);
    }

    /**
     * @Route("/api/posts/{id}", name="post_destroy", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function destroy(int $id)
    {
        $post = $this->postService->findPost($id);
        $this->postService->deletePost($post);

        return $this->responseService->buildResponse('', 204);
    }
}