<?php

namespace App\CaseboxCoreBundle\Event;

use App\CaseboxCoreBundle\Entity\Core;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CaseboxCoreEvent
 */
class CaseboxCoreEvent extends Event
{
    /**
     * @var Core
     */
    protected $core;

    /**
     * QueueEvent constructor
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
    }

    /**
     * @return Core
     */
    public function getCore()
    {
        return $this->core;
    }
}
