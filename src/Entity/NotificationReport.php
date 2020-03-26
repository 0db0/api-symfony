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
     * @ORM\Column(type="integer")
     */
    private $recipientId;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(User $sender, User $recipient)
    {
        $this->sender = $sender;
        $this->recipientId = $recipient->getId();
        $this->status = self::NOTIFICATION_STATUS_PENDING;
        $this->createdAt = new \DateTime();
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

    public function getRecipient(): int
    {
        return $this->recipientId;
    }

    public function setRecipient(User $recipient): self
    {
        $this->recipientId = $recipient->getId();

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