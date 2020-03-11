<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    /** @var UserRepository  */
    private $repository;

    /** @var RequestService  */
    private $requestService;

    public function __construct(UserRepository $repository,
                                RequestService $requestService)
    {
        $this->repository = $repository;
        $this->requestService = $requestService;
    }

    public function createAllUsersList()
    {
        return $this->repository->findAllUsers();
    }

    public function countAllUsers()
    {
        return $this->repository->countAllUsers();
    }

    public function createUsersList()
    {
        $offset = $this->requestService->getOffsetFromQueryString();

        if ($offset === 0 || $offset === 1) {
            return $this->repository
                      ->findUsersByRange(0, PaginationService::LIMIT_USER);
        }

        return $this->repository
                  ->findUsersByRange(($offset-1) * PaginationService::LIMIT_USER, $offset * PaginationService::LIMIT_USER);
    }

    public function findUserById(int $id): User
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function createNewUser(object $data): User
    {
        $user = new User();
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setPassword($data->password);
        $user->setEmail($data->email);

        return $user;
    }
}