<?php

namespace App\Service;

use App\Entity\NotificationEmail;
use App\Utils\BaseEmailService;
use Predis\Client;
use Symfony\Component\Mime\Email;

class RedisClient
{

    /** @var Client  */
    private $predis;

    /** @var UserService  */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->predis = new Client();
        $this->userService = $userService;
    }

    public function getAll(): array
    {
         $keys = $this->predis->scan(0, ['match' => BaseEmailService::NOTIFICATION_EMAIL_KEY.'*']);
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

    public function del(string $key): int
    {
        return $this->predis->del([$key]);
    }
}