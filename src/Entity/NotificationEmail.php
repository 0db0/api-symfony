<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;

/**
 *@ORM\Entity(repositoryClass="App\Repository\NotificationEmailRepository")
 */
class NotificationEmail
{
    private const NOTIFICATION_EMAIL_STATUS_PENDING = 'pending';
    public const NOTIFICATION_EMAIL_STATUS_PERFORMED = 'performed';
    public const NOTIFICATION_EMAIL_STATUS_FAILED = 'failed';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $queueId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addressee;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    public function __construct()
    {
        $this->status = self::NOTIFICATION_EMAIL_STATUS_PENDING;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQueueId(): string
    {
        return $this->queueId;
    }

    public function setQueueId(string $queueId): void
    {
        $this->queueId = $queueId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getAddressee(): string
    {
        return $this->addressee;
    }

    public function setAddressee(string $addressee): self
    {
        $this->addressee = $addressee;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}