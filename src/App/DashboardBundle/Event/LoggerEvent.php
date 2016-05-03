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
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var integer
     */
    protected $timestamp;

    /**
     * @var array
     */
    protected $context;

    /**
     * LoggerEvent constructor
     * @param Logger $logger
     * @param string $type
     * @param string $message
     * @param array $context
     */
    public function __construct(Logger $logger, $type = 'success', $message = '', array $context = [])
    {
        $this->logger = $logger;
        $this->type = $type;
        $this->message = $message;
        $this->context = $context;
        $this->timestamp = time();
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
