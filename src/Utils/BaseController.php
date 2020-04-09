<?php

namespace App\Utils;

use App\Service\ResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /** @var ResponseService  */
    private $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    protected function buildResponse($data, int $status, $type = 'json'): Response
    {
        return $this->responseService->buildResponse($data, $status, $type);
    }
}