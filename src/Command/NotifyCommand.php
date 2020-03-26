<?php

namespace App\Command;

use App\Service\EmailService;
use App\Service\UserService;
use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;

class NotifyCommand extends Command
{
    public static $defaultName = 'app:notify-followers';

    /** @var UserService */
    private $userService;

    /** @var RedisAdapter  */
    private $redis;

    /** @var EmailService  */
    private $emailService;

    public function __construct(string $name = null, UserService $userService, EmailService $emailService)
    {
        $this->userService = $userService;
        $this->redis = new RedisAdapter(new Client());
        $this->emailService = $emailService;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->addArgument('user_id', InputArgument::REQUIRED, 'Id of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->userService->findUserById($input->getArgument('user_id'));

        if (!$user) {
            $output->writeln(sprintf('User with id: %d not found', $input->getArgument('user_id')));
        }
        $key = '';
        foreach ($user->getFollowers() as $follower) {
            $key = 'notification_'.$user->getId().'_'.$follower->getId();
            /** @var Email $email */
            $email = $this->redis->get($key, function (){});
            $email = unserialize($email);
            $this->emailService->sendEmail($email);
//            report->changeStatus($status);
            $this->redis->delete($key);

        }

        $output->writeln(sprintf('Email from %s to all followers has been send', $user->getFullName()));
        return 0;
    }
}