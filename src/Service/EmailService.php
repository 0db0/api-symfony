<?php

namespace App\Service;

use App\Entity\NotificationEmail;
use App\Repository\NotificationEmailRepository;
use App\Utils\BaseEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailService extends BaseEmailService
{
    /** @var RedisClient  */
    private $redisClient;

    public function __construct(
        MailerInterface $mailer,
        UserService $userService,
        PostService $postService,
        EntityManagerInterface $em,
        NotificationEmailRepository $emailRepository,
        RedisClient $redisClient
    )
    {
        $this->redisClient = $redisClient;
        parent::__construct($mailer, $userService, $postService, $em, $emailRepository);
    }


    /**
     * @param NotificationEmail[] $emailList
     */
    protected function sendToQueue(array $emailList): void
    {
        foreach ($emailList as $email) {
            $this->redisClient->set($email->getQueueId(), serialize($email));
        }
    }

    /**
     * @param NotificationEmail[] $emailList
     */
    protected function removeEmailsFromQueue(array $emailList)
    {
        foreach ($emailList as $email) {
            if ($email->isPerformed()) {
                $this->redisClient->del($email->getQueueId());
            }
        }
    }
}