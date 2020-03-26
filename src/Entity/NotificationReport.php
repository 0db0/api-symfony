<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationReportRepository")
 */
class NotificationReport
{
    private const NOTIFICATION_STATUS_PENDING = 'pending';
    public const NOTIFICATION_STATUS_PERFORMED = 'performed';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notificationReports")
     */
    private $sender;

    /**
     * @ORM\Column(type="object")
     */
    private $recipient;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $status;

    public function __construct(User $sender, User $recipient)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->status = self::NOTIFICATION_STATUS_PENDING;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function changeStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}