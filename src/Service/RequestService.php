<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestService
{
    private const PARAMETER_OFFSET = 'offset';
    private const PARAMETER_OFFSET_DEFAULT_VALUE = 1;
    private const PARAMETER_LIMIT = 'limit';
    private const PARAMETER_LIMIT_DEFAULT_VALUE = 100;

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

    public function getParameters()
    {
       return $this->getParamsFromQueryStringKeys();
    }

    private function getParamsFromQueryStringKeys()
    {
        $keys = $this->requestStack->getCurrentRequest()->query->keys();

        $params = [];
        if (in_array('offset', $keys)) {
            $params['offset'] = (int) $this->requestStack->getCurrentRequest()->query->get('offset');
        } else {
            $params['offset'] = self::PARAMETER_OFFSET_DEFAULT_VALUE;
        }

        if (in_array('limit', $keys)) {
            $params['limit'] = (int) $this->requestStack->getCurrentRequest()->query->get('limit');
        } else {
            $params['limit'] = self::PARAMETER_LIMIT_DEFAULT_VALUE;
        }

        if (in_array('tags', $keys)) {
            $params['tags'] = $this->requestStack->getCurrentRequest()->query->get('tags');
        }

        return $params;
    }

    public function getContent(): \stdClass
    {
        $content = $this->requestStack->getCurrentRequest()->getContent();

        return json_decode($content);
    }
}