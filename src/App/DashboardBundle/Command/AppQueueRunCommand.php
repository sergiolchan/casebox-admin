<?php

namespace App\DashboardBundle\Command;

use App\DashboardBundle\Service\QueueService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AppQueueRunCommand
 */
class AppQueueRunCommand extends ContainerAwareCommand
{
    const APP_QUEUE_RUN_COMMAND = 'app:queue:run';

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName(self::APP_QUEUE_RUN_COMMAND)
            ->setDescription('Run queue commands.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        /** @var QueueService $queue */
        $queue = $this->getContainer()->get('app_dashboard.service.queue_service');
        $queue->queueRead(QueueService::APP_QUEUE_COMMAND);

        $output->success("[x] DONE!");
    }
}
