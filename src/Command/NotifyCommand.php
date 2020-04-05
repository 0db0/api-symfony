<?php

namespace App\Command;

use App\Service\EmailService;
use App\Service\RedisClient;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends Command
{
    public static $defaultName = 'app:notify-followers';

    /** @var EmailService  */
    private $emailService;

    /** @var RedisClient */
    private $redisClient;


    public function __construct(
        EmailService $emailService,
        RedisClient $redisClient
    ) {
        $this->emailService = $emailService;
        $this->redisClient  = $redisClient;

        parent::__construct();
    }

    protected function configure() { }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $isWork = $redisClient->has('notify_command');
//        if ($isWork) {
//            return 0;
//        } else {
//            $redisClient->set('notify_command');
//        }

        $emailList = $this->redisClient->getAllEmail();

        foreach ($emailList as $email) {
            $this->emailService->sendEmail($email);
        }

        return 0;
    }
}