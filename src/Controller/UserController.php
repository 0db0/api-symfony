<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ResponseService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /** @var UserService  */
    private $userService;

    /** @var ResponseService  */
    private $responseService;

    public function __construct(UserService $userService, ResponseService $responseService)
    {
        $this->userService = $userService;
        $this->responseService = $responseService;
    }

    /**
     * @Route("/api/users", name="users_list", methods={"GET"})
     */
    public function list()
    {
        $userList = $this->userService->createUsersList();
        $response = $this->responseService->buildResponse($userList, 200, 'hal+json');

        return $response;
    }

    /**
     * @Route("/api/users/{id}", name="user_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(int $id)
    {
        $user = $this->userService->findUserById($id);

        return $this->responseService->buildResponse($user, 200);
    }

    /**
     * @Route("/api/users", name="user_create", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent());

        $user = $this->userService->createNewUser($data);

        $this->save($user);

        return $this->responseService->buildResponse($user, 201);
    }
// todo: Реализовать PostController через Dto object + use Redis for highload
    private function save(User $user): void
    {
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();
    }
}