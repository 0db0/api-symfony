<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Service\ResponseService;
use App\Service\UserService;
use App\Utils\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /** @var UserService  */
    private $userService;

    public function __construct(UserService $userService, ResponseService $responseService)
    {
        $this->userService = $userService;

        parent::__construct($responseService);
    }

    /**
     * @Route("/api/users", name="users_list", methods={"GET"})
     */
    public function list()
    {

        $userList = $this->userService->createUsersList();

        return $this->buildResponse($userList, 200, 'hal+json');
    }

    /**
     * @Route("/api/users/{id}", name="user_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(int $id)
    {
        $user = $this->userService->findUserById($id);

        return $this->buildResponse($user, 200);
    }

    /**
     * @Route("/api/users", name="user_create", methods={"POST"})
     * @ParamConverter()
     */
    public function create(CreateUserDto $userDto)
    {
        dd($userDto);

        $user = $this->userService->createNewUser($userDto);

        return $this->buildResponse($user, 201);
    }
}