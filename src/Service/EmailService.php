<?php

namespace App\Service;

use App\Entity\NotificationEmail;
use App\Entity\Post;
use App\Repository\NotificationEmailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    /** @var MailerInterface  */
    private $mailer;

    /** @var UserService  */
    private $userService;

    /** @var PostService  */
    private $postService;

    /** @var EntityManagerInterface  */
    private $em;

    /** @var RedisClient  */
    private $redisClient;

    /** @var NotificationEmailRepository  */
    private $emailRepository;

    public function __construct(
        MailerInterface $mailer,
        UserService $userService,
        PostService $postService,
        EntityManagerInterface $em,
        RedisClient $redisClient,
        NotificationEmailRepository $emailRepository

    )
    {
        $this->mailer = $mailer;
        $this->userService = $userService;
        $this->postService = $postService;
        $this->em = $em;
        $this->redisClient = $redisClient;
        $this->emailRepository = $emailRepository;
    }

    public function getAllEmails(): array
    {
        return $this->emailRepository->getAllEmails();
    }

    public function sendNotificationEmail(Post $post)
    {
        $notificationEmailList = $this->createNotificationEmailsForNewPost($post);
        $this->sendToQueue($notificationEmailList);
        $this->saveEmails($notificationEmailList);
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

    private function sendToQueue(array $emailList): void
    {
        foreach ($emailList as $email) {
            $queueId = $this->redisClient->generateQueueIdForEmail($email);
            $email->setQueueId($queueId);
            $this->redisClient->set($queueId, $email);
        }
    }

    private function saveEmails(array $emailList): void
    {
        foreach ($emailList as $email) {
            $this->em->persist($email);
        }

        $this->em->flush();
    }

    /**
     * @param NotificationEmail[] $emailList
     */
    public function sendEmails(array $emailList): void
    {
       foreach ($emailList as $email) {
           $preparedNotificationEmail = $this->prepareNotificationEmailForSending($email);

           try {
               echo 'Email was send!'."\n";
//            $this->mailer->send($preparedNotificationEmail);
               $email->setStatus(NotificationEmail::NOTIFICATION_EMAIL_STATUS_PERFORMED);
           } catch (TransportExceptionInterface $exception) {
//loggin
               $email->setStatus(NotificationEmail::NOTIFICATION_EMAIL_STATUS_FAILED);
           }
           $this->em->persist($email);
           $this->em->flush();

           $this->removeFromQueue($email);
       }
    }

    public function sendEmail(NotificationEmail $email): void
    {
//        foreach ()

        $preparedNotificationEmail = $this->prepareNotificationEmailForSending($email);

        try {
            echo 'Email was send!'."\n";
//            $this->mailer->send($preparedNotificationEmail);
            $email->setStatus(NotificationEmail::NOTIFICATION_EMAIL_STATUS_PERFORMED);
        } catch (TransportExceptionInterface $exception) {
//loggin
            $email->setStatus(NotificationEmail::NOTIFICATION_EMAIL_STATUS_FAILED);
        }
        $this->em->persist($email);
        $this->em->flush();

        $this->removeFromQueue($email);
    }

    private function removeFromQueue(NotificationEmail $email)
    {
        $this->emailRepository->deleteEmail($email);
    }

    private function prepareNotificationEmailForSending(NotificationEmail $notificationEmail): Email
    {
        return (new Email())
            ->from($notificationEmail->getSender())
            ->to($notificationEmail->getAddressee())
            ->subject($notificationEmail->getSubject())
            ->text($notificationEmail->getText());
    }
}