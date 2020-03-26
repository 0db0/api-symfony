<?php

namespace App\Service;

use App\Entity\NotificationReport;
use App\Entity\User;
use App\Repository\NotificationReportRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationReportService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var NotificationReportRepository  */
    private $reportRepository;

    public function __construct(EntityManagerInterface $em, NotificationReportRepository $reportRepository)
    {
        $this->em = $em;
        $this->reportRepository = $reportRepository;
    }

    public function persist(User $sender, User $recipient): void
    {
        $report = $this->createNewNotificationReport($sender, $recipient);

        $this->save($report);
    }

    public function performed(NotificationReport $report): void
    {
        $report->changeStatus(NotificationReport::NOTIFICATION_STATUS_PERFORMED);
        $this->save($report);
    }

    public function getNotificationReport(User $sender, User $follower): NotificationReport
    {
        return $this->reportRepository->findOneBy(['sender' => $sender, 'recipientId' => $follower->getId()]);
    }

    private function createNewNotificationReport(User $sender, User $recipient): NotificationReport
    {
        return new NotificationReport($sender, $recipient);
    }

    private function save(NotificationReport $report): void
    {
        $this->em->persist($report);
        $this->em->flush();
    }
}