<?php

namespace App\Utils;

use App\Entity\NotificationEmail;
use App\Entity\Post;
use App\Repository\NotificationEmailRepository;
use App\Service\PostService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

abstract class BaseEmailService
{
    public const NOTIFICATION_EMAIL_KEY = 'notifications:post_emails:';

    /** @var MailerInterface  */
    private $mailer;

    /** @var UserService  */
    private $userService;

    /** @var PostService  */
    private $postService;

    /** @var EntityManagerInterface  */
    private $em;

    /** @var NotificationEmailRepository  */
    private $emailRepository;

    public function __construct(
        MailerInterface $mailer,
        UserService $userService,
        PostService $postService,
        EntityManagerInterface $em,
        NotificationEmailRepository $emailRepository
    )
    {
        $this->mailer          = $mailer;
        $this->userService     = $userService;
        $this->postService     = $postService;
        $this->em              = $em;
        $this->emailRepository = $emailRepository;
    }

    abstract protected function sendToQueue(array $emailList): void;

    abstract protected function removeEmailsFromQueue(array $emailList);

    final public function saveNotificationEmail(Post $post)
    {
        $notificationEmailList = $this->createNotificationEmailsForNewPost($post);

        $this->saveEmails($notificationEmailList);
        $this->sendToQueue($notificationEmailList);
    }


    /**
     * @return NotificationEmail[]
     */
    public function getAllEmails(): array
    {
        return $this->emailRepository->findAllEmails();
    }


    public function sendNotificationEmails(array &$notificationEmailList): void
    {
        $this->sendEmails($notificationEmailList);
        $this->removeEmailsFromQueue($notificationEmailList);
    }

    protected function generateQueueIdForEmail(NotificationEmail $email): string
    {
        $author = $this->userService->getUserByEmail($email->getSender());
        $follower = $this->userService->getUserByEmail($email->getAddressee());

        return self::NOTIFICATION_EMAIL_KEY . $author->getId() . ':'. $follower->getId();
    }

    private function createNotificationEmailsForNewPost(Post $post): array
    {
        $author = $this->postService->getPostAuthor($post);
        $NotificationEmailList = [];

        foreach ($author->getFollowers() as $follower) {
            $NotificationEmailList[] = (new NotificationEmail())
                ->setSender($post->getAuthor()->getEmail())
                ->setAddressee($follower->getEmail())
                ->setSubject(
                    sprintf(
                        'New post from %s. Check this out!',
                        $post->getAuthor()->getFullName()
                    ))
                ->setText(
                    sprintf(
                        '%s, your favorite following just released a post. Let`s see what`s new!',
                        $follower->getFullName()
                    ));
        }

        return $NotificationEmailList;
    }

    private function saveEmails(array $emailList): void
    {
        foreach ($emailList as $email) {
            $queueId = $this->generateQueueIdForEmail($email);
            $email->setQueueId($queueId);
            $this->em->persist($email);
        }
        $this->em->flush();
    }

    // Перенести этот метод в NotificationEmail? NotificationEmail::getMimeEmail()
    private function prepareNotificationEmailsForSending(NotificationEmail $notificationEmail): Email
    {
        return $email = (new Email())
            ->from($notificationEmail->getSender())
            ->to($notificationEmail->getAddressee())
            ->subject($notificationEmail->getSubject())
            ->text($notificationEmail->getText());
    }

    private function sendEmails(array $notificationEmailList): void
    {
        foreach ($notificationEmailList as &$notificationEmail) {
            $preparedEmail = $this->prepareNotificationEmailsForSending($notificationEmail);
            try {
                echo 'Email was send!'."\n";
//            $this->mailer->send($preparedNotificationEmail);
                $notificationEmail->setStatus(NotificationEmail::NOTIFICATION_EMAIL_STATUS_PERFORMED);
            } catch (TransportExceptionInterface $exception) {
//            loggin
                $notificationEmail->setStatus(NotificationEmail::NOTIFICATION_EMAIL_STATUS_FAILED);
            }
            $this->em->flush();
        }
    }
}