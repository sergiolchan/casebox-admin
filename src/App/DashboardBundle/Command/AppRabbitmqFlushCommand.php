<?php

namespace App\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppRabbitmqFlushCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:rabbitmq:flush')
            ->setDescription('Flush rabbitmq queue.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        $this->getContainer()->get('app_dashboard.service.queue_service')->queueClear();

        $output->success("DONE!");
    }

}
