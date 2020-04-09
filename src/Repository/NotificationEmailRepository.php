<?php

namespace App\Repository;

use App\Entity\NotificationEmail;
use App\Service\RedisClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationEmailRepository extends ServiceEntityRepository
{
    /** @var RedisClient  */
    private $redisClient;

    public function __construct(ManagerRegistry $registry, RedisClient $redisClient)
    {
        $this->redisClient = $redisClient;
        parent::__construct($registry, NotificationEmail::class);
    }

    public function findAllEmails(): array
    {
        return $this->redisClient->getAll();
    }

    public function deleteEmail(NotificationEmail $email)
    {
        $this->redisClient->delete($email->getQueueId());
    }
}