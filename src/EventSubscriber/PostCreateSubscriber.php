<?php

namespace App\EventSubscriber;

use App\Event\PostCreatedEvent;
use App\Service\EmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostCreateSubscriber implements EventSubscriberInterface
{
    /** @var EmailService  */
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function onPostCreate(PostCreatedEvent $event)
    {
        $post = $event->getPost();
        $this->emailService->saveNotificationEmail($post);
    }

    public static function getSubscribedEvents()
    {
        return [
            PostCreatedEvent::NAME => 'onPostCreate',
        ];
    }
}