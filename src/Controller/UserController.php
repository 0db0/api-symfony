<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Service\ResponseService;
use App\Service\UserService;
use App\Utils\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /** @var UserService  */
    private $userService;

    public function __construct(UserService $userService, ResponseService $responseService)
    {

        parent::__construct($responseService);
        $this->userService = $userService;
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
    public function show(int $id, Session $session)
    {

        $user = $this->userService->getUserById($id);


        return $this->buildResponse($user, 200);
    }

    /**
     * @Route("/api/users", name="user_create", methods={"POST"})
     * @ParamConverter()
     */
    public function create(CreateUserDto $userDto)
    {
        $userDto->setFromArray([
            'email' => 1,
            'password' => 2,
            'first_name' => 3,
            'last_name' => 4,
            'count_of_page' => 5,
        ]);
        $user = $this->userService->createNewUser($userDto);

        return $this->buildResponse($user, 201);
    }
}