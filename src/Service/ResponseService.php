<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
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

        $response = [
            'ok' => 'true',
            'user' => [
                'id'        => $data->getId(),
                'firstName' => $data->getFirstName(),
                'lastName'  => $data->getLastName(),
                'email'     => $data->getEmail(),
            ],
            "_links" => [
                'self' => [
                    'href' => $this->requestService->getBaseUrl(),
                ],
            ],
        ];

        if ($this->requestService->getMethod() === 'POST') {
            $response['_links']['self']['href'] = $this->requestService->getBaseUrl() . '/' . $data->getId();
        }

        return $response;
    }

    private function prepareResponseFromCollection(array $data): array
    {
        $response = $this->pagination->paginate($data);

        return $response;
    }
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