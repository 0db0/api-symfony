<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestService
{
    private const PARAMETER_OFFSET_DEFAULT_VALUE = 1;
    private const PARAMETER_LIMIT_DEFAULT_VALUE = 100;
    private const PARAMETER_TAGS_DEFAULT_VALUE = '';

    /** @var Request */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getBaseUrl(): string
    {
        return $this->request->getSchemeAndHttpHost() . $this->request->getPathInfo();
    }

    public function getOffsetFromQueryString(): int
    {
        return (int) $this->request->get('page');
    }

    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    public function getContent()
    {
        $content = $this->request->getContent();

        return json_decode($content);
    }

    public function getParameters(): array
    {
        return $this->getParamsFromQueryStringKeys();
    }

    private function getParamsFromQueryStringKeys(): array
    {
        $keys = $this->request->query->keys();

        $params = [];
        if (in_array('offset', $keys)) {
            $params['offset'] = (int) $this->request->query->get('offset');
        } else {
            $params['offset'] = self::PARAMETER_OFFSET_DEFAULT_VALUE;
        }

        if (in_array('limit', $keys)) {
            $params['limit'] = (int) $this->request->query->get('limit');
        } else {
            $params['limit'] = self::PARAMETER_LIMIT_DEFAULT_VALUE;
        }

        if (in_array('tags', $keys)) {
            $params['tags'] = $this->request->query->get('tags');
        } else {
            $params['tags'] = self::PARAMETER_TAGS_DEFAULT_VALUE;
        }

        return $params;
    }
}