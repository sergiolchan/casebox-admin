<?php

namespace App\DashboardBundle\Command;

use App\DashboardBundle\Service\MessageService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class AppQueueStartCommand
 */
class AppQueueStartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:queue:start')
            ->setDescription('Start queue processing in background mode.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $cliOutput = $output);

        if (!extension_loaded('pcntl')) {
            $output->error([MessageService::PROCESS_PCNTL_NOT_FOUND]);

            return 1;
        }

        $lockFile = $this->getLockFile(AppQueueRunCommand::APP_QUEUE_RUN_COMMAND);
        if (file_exists($lockFile)) {
            $output->error([MessageService::PROCESS_QUEUE_RUNNING]);

            return 1;
        }

        $pid = pcntl_fork();

        if ($pid < 0) {
            $output->error(MessageService::PROCESS_QUEUE_UNABLE_TO_START);

            return;
        }

        if ($pid > 0) {
            $output->success(sprintf(MessageService::PROCESS_QUEUE_STARTED));

            return;
        }

        if (posix_setsid() < 0) {
            $output->error(MessageService::PROCESS_CHILD_NOT_FOUND);

            return 1;
        }

        if (null === $process = $this->createQueueProcess($output)) {
            return 1;
        }

        $process->disableOutput();
        $process->start();
        $lockFile = $this->getLockFile(AppQueueRunCommand::APP_QUEUE_RUN_COMMAND);
        touch($lockFile);

        if (!$process->isRunning()) {
            $output->error(MessageService::PROCESS_QUEUE_UNABLE_TO_START);
            unlink($lockFile);

            return 1;
        }

        // stop the web queue when the lock file is removed
        while ($process->isRunning()) {
            if (!file_exists($lockFile)) {
                $process->stop();
            }

            sleep(1);
        }
    }

    /**
     * @param SymfonyStyle $output
     *
     * @return Process The process
     */
    private function createQueueProcess(SymfonyStyle $output)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $command = $rootDir.'/../bin/console';

        $finder = new PhpExecutableFinder();
        if (false === $binary = $finder->find()) {
            $output->error(MessageService::PROCESS_PHP_NOT_FOUND);

            return null;
        }

        $script = implode(' ', array_map(array('Symfony\Component\Process\ProcessUtils', 'escapeArgument'), array(
            $binary,
            $command,
            AppQueueRunCommand::APP_QUEUE_RUN_COMMAND
        )));

        return new Process('exec '.$script, $rootDir, null, null, null);
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
