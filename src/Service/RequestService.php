<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestService
{
    /** @var RequestStack  */
    private $requestStack;

    /** @var Request */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function getCurrentUrl(): string
    {
        return $this->request->getUri();
    }

    public function getBaseUrl(): string
    {
        return $this->request->getSchemeAndHttpHost() . $this->request->getPathInfo();
    }

    public function getSchemeAndHttpHost(): string
    {
        return $this->request->getSchemeAndHttpHost();
    }

    public function getOffsetFromQueryString(): int
    {
        return (int) $this->request->get('page');
    }

    public function getMethod(): string
    {
        return $this->request->getMethod();
    }
}