<?php

namespace App\Service;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class ResponseService
{
    /** @var PaginationService  */
    private $pagination;

    /** @var RequestService  */
    private $requestService;

    public function __construct(PaginationService $pagination, RequestService $requestService)
    {

        $this->pagination = $pagination;
        $this->requestService = $requestService;
    }

//    /**
//     * @param array  $data
//     * @param int    $status HTTP response status code
//     * @param string $type   Part of MIME-type used in header Content-Type
//     * @return Response
//     */
//    public function createResponse(array $data, int $status, string $type): Response
//    {
//        $list = $this->pagination->paginate($data);
//
//        return new JsonResponse($list, $status, [
//            'Content-Type' => 'application/'.$type,
//        ]);
//    }

    public function buildResponse($data, int $status, string $type = 'json'): Response
    {
        $response = [];

        if (is_null($data)) {
            $response = $this->createBadResponse();
        }

        if (is_object($data)) {
            $response = $this->prepareResponseFromObject($data);
        }

        if (is_array($data)) {
            $response = $this->prepareResponseFromCollection($data);
        }

        return new JsonResponse($response, $status, [
            'Content-Type' => 'application/' . $type,
        ]);
    }

    private function prepareResponseFromObject(object $data): array
    {
        if ($data instanceof User) {
            return $this->createResponseForUser($data);
        }

        if ($data instanceof Post) {
            return $this->createResponseForPost($data);
        }
    }

//    private function prepareResponseFromCollection(array $data): array
//    {
//        return $this->pagination->paginate($data);
//
//    }

    private function prepareResponseFromCollection (array $collection): array
    {
        $response = [
            "ok" => "true",
            'count' => count($collection),
            'items' => [
            ],
        ];

        foreach ($collection as $item) {
            array_push($response['items'], $item->getId());
        }

        return $response;
    }

    private function createResponseForUser(User $user)
    {
        $response = [
            'ok' => 'true',
            'user' => [
                'id'        => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName'  => $user->getLastName(),
                'email'     => $user->getEmail(),
            ],
            "_links" => [
                'self' => [
                    'href' => $this->requestService->getBaseUrl(),
                ],
            ],
        ];

        if ($this->requestService->getMethod() === 'POST') {
            $response['_links']['self']['href'] = $this->requestService->getBaseUrl() . '/' . $user->getId();
        }

        return $response;
    }

    private function createResponseForPost(Post $post)
    {
        $response = [
            'ok' => 'true',
            'posts' => [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'text' => $post->getText(),
                'author' => $post->getAuthor()->getId(),
                'tags' => [
                    'count' => $post->getTags()->count(),
                    'titles' => $this->getTags($post),
                ],
            ],
            "_links" => [
                'self' => [
                    'href' => $this->requestService->getBaseUrl(),
                ],
            ],
        ];

        if ($this->requestService->getMethod() === 'POST') {
            $response['_links']['self']['href'] = $this->requestService->getBaseUrl() . '/' . $post->getId();
        }

        return $response;
    }

    private function getTags(Post $post): array
    {
        $tags = $post->getTags()->toArray();
        $titles = [];

        foreach ($tags as $tag) {
            $titles[] = $tag->getTitle();
        }

        return $titles;
    }

    private function createBadResponse()
    {
        $response = [
            'ok' => 'false',
            'error' => [
                'error_code' => 0,
                'error_msg' => 'Item not found',
//                'request_params' => $this->getRequestParameters($params),
            ],
        ];

        return $response;
    }

//    private function getRequestParameters($params): array
//    {
//
//        $requestParams = [];
//        foreach ($params as $key => $value) {
//            $requestParams[] = ['key' => $key, 'value' => $value];
//        }
//        return $requestParams;
//    }
}


//
//foreach ($blogList as $blog) {
//    $response['blogList'][] = [
//        'id' => $blog->getId(),
//        'title' => $blog->getTitle(),
//        'text' => $blog->getBlog(),
//        'commentsCount' => count($blog->getComments()),
//        'createdAt' => $this->getDate($blog),
//        '_links' => [
//            'self' => [
//                'href' => '/api/blog/'.$blog->getId().'/'.$blog->getSlug(),
//            ],
//        ],
//    ];