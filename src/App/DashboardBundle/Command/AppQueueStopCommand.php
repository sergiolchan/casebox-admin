<?php

namespace App\DashboardBundle\Command;

use App\DashboardBundle\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AppQueueStopCommand
 */
class AppQueueStopCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:queue:stop')
            ->setDescription('Stop queue processing')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        $lockFile = $this->getLockFile(AppQueueRunCommand::APP_QUEUE_RUN_COMMAND);

        if (!file_exists($lockFile)) {
            $output->error(MessageService::PROCESS_QUEUE_NOT_RUNNING);

            return 1;
        }

        unlink($lockFile);
        $output->success(MessageService::PROCESS_QUEUE_STOPPED);
    }

    /**
     * @param string $lock
     *
     * @return string The filename
     */
    protected function getLockFile($lock)
    {
        return sys_get_temp_dir().'/'.strtr($lock, '.:', '--').'.pid';
    }
}
