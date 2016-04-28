<?php

namespace App\EcryptFsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AppEcryptfsUmountCommand
 */
class AppEcryptfsUmountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:ecryptfs:umount')
            ->setDescription('Unmount encrypted folders.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        // code...

        $output->success("[x] DONE!");
    }
}
