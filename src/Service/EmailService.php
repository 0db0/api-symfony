<?php

namespace App\Service;

use App\Entity\NotificationReport;
use App\Entity\Post;
use App\Entity\User;
use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Cache\ItemInterface;

class EmailService
{
    /** @var MailerInterface  */
    private $mailer;

    /** @var RedisAdapter  */
    private $redisService;

    /** @var NotificationReportService  */
    private $notificationReportService;

    public function __construct(MailerInterface $mailer, NotificationReportService $notificationReportService)
    {
        $this->mailer = $mailer;
        $this->redisService = new RedisAdapter(new Client());
        $this->notificationReportService = $notificationReportService;
    }

    public function saveEmail(Post $post, $followers): bool
    {
        /** @var User $follower */
        foreach ($followers as $follower) {
            $email = (new Email())
                ->from($post->getAuthor()->getEmail())
                ->to($follower->getEmail())
                ->subject(
                    sprintf(
                        'New post from %s. Check this out!',
                        $post->getAuthor()->getFullName()
                    ))
                ->text(sprintf('%s, your favorite following just released a post. Let`s see what`s new!', $follower->getFullName()));

            $key = sprintf('notification_%s_%s', $post->getAuthor()->getId(), $follower->getId());

            $this->redisService->get($key, function (ItemInterface $item) use ($email) {
                return serialize($email);
            });

            $this->notificationReportService->persist($post->getAuthor(), $follower);
        }

        return true;
    }

    public function sendEmail(Email $email)
    {
        echo 'Email was send!'."\n";
//        $this->mailer->send($email);
    }
}