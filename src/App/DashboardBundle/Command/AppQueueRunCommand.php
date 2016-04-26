<?php

namespace App\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        /** @var ContainerQueueService $queue */
        $queue = $this->getContainer()->get('stack_container.service.container_queue_service');
        $queue->queueRead(ContainerQueueService::QUEUE_CONTAINER);

        $output->success("[x] DONE!");
    }

}
