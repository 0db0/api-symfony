<?php

namespace App\Utils;

use App\Dto\CreatePostDto;
use App\Entity\Post;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;

class CreatePostDtoAssembler
{
    /** @var UserService  */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function readCreatePostDto(CreatePostDto $createPostDto, Post $post): Post
    {
        $author = $this->userService->getUserById($createPostDto->getAuthorId());

        if (! $post) {
            $post = new Post(
                $createPostDto->getTitle(),
                $createPostDto->getText(),
                $author
            );
        }

        return $post;
    }

    public function writeCreatePostDto(Request $request)
    {
        $content = json_decode($request->getContent());

        return new CreatePostDto($content->title, $content->text, $content->author);
    }
}