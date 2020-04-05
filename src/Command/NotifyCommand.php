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
    /** @var EmailService  */
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:notify-followers')
            ->setDescription('Send notification email to followers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emailList = $this->emailService->getAllEmails();

//        foreach ($emailList as $email) {
//            $this->emailService->sendEmail($email);
//        }

            $this->emailService->sendEmails($emailList);

        return 0;
    }
}