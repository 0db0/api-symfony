<?php

namespace App\Service;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    /** @var UserRepository  */
    private $repository;

    /** @var RequestService  */
    private $requestService;

    /** @var EntityManagerInterface  */
    private $entityManager;

    public function __construct(UserRepository $repository,
                                RequestService $requestService,
                                EntityManagerInterface $entityManager
    )
    {
        $this->repository = $repository;
        $this->requestService = $requestService;
        $this->entityManager = $entityManager;
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
                  ->findUsersByRange(($offset - 1) * PaginationService::LIMIT_USER, $offset * PaginationService::LIMIT_USER);
    }

    public function getUserById(int $id): User
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function createNewUser(CreateUserDto $userDto): User
    {
        $user = new User(
            $userDto->getEmail(),
            $userDto->getPassword(),
            $userDto->getFirstName(),
            $userDto->getLastName()
        );

        $this->save($user);

    }

    private function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}