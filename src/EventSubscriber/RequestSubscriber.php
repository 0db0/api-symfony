<?php

namespace App\EventSubscriber;

use App\Utils\CreatePostDtoAssembler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestSubscriber implements EventSubscriberInterface
{
    /** @var CreatePostDtoAssembler  */
    private $dtoAssembler;

    public function __construct(CreatePostDtoAssembler $dtoAssembler)
    {
        $this->dtoAssembler = $dtoAssembler;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
//dd($request);

//        if ($request->isMethod('POST') && $request->getRequestUri() == '/api/posts') {
//           $createPostDto = $this->dtoAssembler->writeCreatePostDto($request);
//           $request->attributes->set('requestDto', $createPostDto);
//        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}