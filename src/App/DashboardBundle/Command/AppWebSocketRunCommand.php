<?php

namespace App\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AppWebSocketRunCommand
 */
class AppWebSocketRunCommand extends ContainerAwareCommand
{
    /**
     * Config
     */
    protected function configure()
    {
        $this
            ->setName('app:websocket:run')
            ->setDescription('Run websocket server.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);


        $output->success(" [x] DONE!");
    }

}
