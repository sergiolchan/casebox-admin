<?php

namespace App\DashboardBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class QueueEvent
 */
class QueueEvent extends Event
{
    /**
     * @var array
     */
    protected $params;

    /**
     * QueueEvent constructor
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
