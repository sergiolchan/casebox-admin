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
    // CryptFS
    const PASSPHRASE_NOT_FOUND          = 'Passphrase not found.';
    const CRYPTFS_UNABLE_TO_MOUNT       = 'Unable to mount encrypted folders.';
    const CRYPTFS_PLEASE_WAIT           = 'Prepare encrypted folders. Will be ready in few seconds.';
    // Service
    const SERVICE_NOT_FOUND             = 'Service not found.';
    const SERVICE_RESTARTED             = '\'%s\' service has been restarted.';
}
