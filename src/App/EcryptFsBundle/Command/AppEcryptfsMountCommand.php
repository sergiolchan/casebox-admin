<?php

namespace App\EcryptFsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppEcryptfsMountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:ecryptfs:mount')
            ->setDescription('Mount encrypted folders.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        // code...

        $output->success("[x] DONE!");
    }
}
