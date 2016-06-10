<?php

namespace App\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppRedisFlushCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:redis:flush')
            ->setDescription('Flush redis cache.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        $this->getContainer()->get('app_dashboard.service.redis_service')->flushAll();

        $output->success("DONE!");
    }

}
