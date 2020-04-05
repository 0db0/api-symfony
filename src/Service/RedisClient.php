<?php

namespace App\Service;

use App\Entity\NotificationEmail;
use Predis\Client;
use Symfony\Component\Mime\Email;

class RedisClient
{
    private const NOTIFICATION_EMAIL_KEY = 'notifications:post_emails:';

    /** @var Client  */
    private $predis;

    /** @var UserService  */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->predis = new Client();
        $this->userService = $userService;
    }

    public function getAllEmail(): array
    {
         $keys = $this->predis->scan(0, ['match' => self::NOTIFICATION_EMAIL_KEY.'*']);
         $valuesList = $this->predis->mget($keys[1]);
         $emailList = [];
         foreach ($valuesList as $value) {
             $emailList[] = unserialize($value);
         }

         return $emailList;
    }

    public function set(string $key, $value)
    {
        if (is_object($value)) {
            $value = serialize($value);
        }

        $this->predis->set($key, $value);
    }

    public function delete(string $key): int
    {
        return $this->predis->del([$key]);
    }

    public function generateQueueIdForEmail(NotificationEmail $email): string
    {
        $author = $this->userService->getUserByEmail($email->getSender());
        $follower = $this->userService->getUserByEmail($email->getAddressee());

        return self::NOTIFICATION_EMAIL_KEY . $author->getId() . ':'. $follower->getId();
    }
}