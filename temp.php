<?php

namespace Telephony\WebBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Shared\CallBoxBundle\Service\CurrentCallService;

/**
 * Class CheckCurrentCallsCommand
 * @package Telephony\WebBundle\Command
 */
class CheckCurrentCallsCommand extends Command
{
    /**
     * @var CurrentCallService $currentCallService
     */
    protected $currentCallService;

    /**
     * @param CurrentCallService $currentCallService
     */
    public function __construct(CurrentCallService $currentCallService)
    {
        $this->currentCallService = $currentCallService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('telephony:check:current-calls')
            ->setDescription('Check current calls stored in redis');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->currentCallService->checkCurrentCalls();

        return 0;
    }
}

// [2020-04-06T17:18:23.176456+03:00] app.INFO: [ts->chatserver] Publish call event to chatserver {"chatserver_name":"node","event":"end","data":{"type":"incoming","site_id":1181832,"call_id":"2472D42D-2475-4FA8-8F77-9E9AF5D5A99D","side":"from","record_url":""}} {"url":"/web/telephony/1/sites/1181832/vox","ip":"::1","http_method":"POST","server":"telephony.ts.dev.jivosite.com","referrer":null,"request_id":"5e8b3a2f13415"}