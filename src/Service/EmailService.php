<?php

namespace App\Service;

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

    private $redisService;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->redisService = new RedisAdapter(new Client());
    }

    public function saveEmail(Post $post, $followers): bool
    {
        foreach ($followers as $follower) {
            $email = (new Email())
                ->from($post->getAuthor()->getEmail())
                ->to($follower->getEmail())
                ->subject(
                    sprintf(
                        'New post from %s. Check this out!',
                        $post->getAuthor()->getFullName()
                    ))
                ->text('Your favorite following just released a post. Let`s see what`s new!');

            $key = sprintf('notification_%s_%s', $post->getAuthor()->getId(), $follower->getId());
            $this->redisService->get($key, function (ItemInterface $item) use ($email) {
                $item->set(serialize($email));
            });
        }
        
        return true;
    }
}