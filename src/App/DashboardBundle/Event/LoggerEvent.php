<?php

namespace App\DashboardBundle\Event;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class LoggerEvent
 */
class LoggerEvent extends Event
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * LoggerEvent constructor
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
