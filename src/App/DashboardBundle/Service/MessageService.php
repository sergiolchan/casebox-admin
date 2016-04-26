<?php

namespace App\DashboardBundle\Service;

class MessageService
{
    // Process
    const PROCESS_PCNTL_NOT_FOUND       = 'This command needs the pcntl extension to run.';
    const PROCESS_PHP_NOT_FOUND         = 'Unable to find PHP binary to start queue process.';
    const PROCESS_CHILD_NOT_FOUND       = 'Unable to set the child process as session leader';
    const PROCESS_QUEUE_UNABLE_TO_START = 'Unable to start the queue process.';
    const PROCESS_QUEUE_STARTED         = 'Queue process started.';
    const PROCESS_QUEUE_RUNNING         = 'A queue process is already running.';
    const PROCESS_QUEUE_NOT_RUNNING     = 'No queue process running.';
    const PROCESS_QUEUE_STOPPED         = 'Stopped the queue process.';
}
