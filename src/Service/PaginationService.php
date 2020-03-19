<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class PaginationService
{
    const LIMIT_USER = 5;

    /** @var RequestService  */
    private $requestService;

    /** @var UserRepository  */
    private $userRepository;


    public function __construct(RequestService $requestService, UserRepository $userRepository)
    {
        $this->requestService = $requestService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User[] $userList
     * @return array
     */
    private function prepareUserListForJson($userList)
    {
        $data = ['ok' => 'true'];

        foreach ($userList as $user) {
            $data['users'][] = [
                'id'        => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName'  => $user->getLastName(),
                'email'     => $user->getEmail(),
            ];
        }

        return $data;
    }

    public function prepareItemListForJson($itemList)
    {
        $data = ['ok' => 'true'];

        foreach ($itemList as $item) {
            $data['items'][] = [
                'id'        => $item->getId(),
                'title'     => $item->getTitle(),
                'text'      => $item->getText(),
                'createdAt' => $item->getCreatedAt(),
            ];
        }

        return $data;
    }

    public function paginate(array $userList)
    {
        $data = $this->prepareUserListForJson($userList);
        $numberPage = $this->getNumberPage();
        $countAllUsers = $this->userRepository->countAllUsers();
        $countPage = (int) ceil($countAllUsers / self::LIMIT_USER);
        $data['pagination'] = [
            'self' => [
                'href' => $this->requestService->getCurrentUrl(),
            ],
            'first' => [
                'href' => $this->requestService->getBaseUrl(),
            ],
            'prev' => [
                'href' => $this->requestService->getBaseUrl().'?page='.($numberPage - 1),
            ],
            'next' => [
                'href' => $this->requestService->getBaseUrl().'?page='.($numberPage + 1),
            ],
            'last' => [
                'href' => $this->requestService->getBaseUrl().'?page='.$countPage,
            ],
        ];

        if ($numberPage === 0 || $numberPage === 1) {
            $data['pagination']['next'] = [
                'href' => $this->requestService->getBaseUrl() . '?page=2',
            ];
            unset($data['pagination']['prev']);
        }

        if ($numberPage === $countPage) {
            unset($data['pagination']['next']);
        }

        return $data;
    }

    private function getNumberPage(): int
    {
        return (int) $this->requestService->getOffsetFromQueryString();
    }
}