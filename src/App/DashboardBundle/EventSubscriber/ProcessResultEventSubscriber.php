<?php

namespace App\DashboardBundle\EventSubscriber;

use App\DashboardBundle\Event\ProcessResultEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ProcessResultEventSubscriber
 */
class ProcessResultEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param ProcessResultEvent $event
     */
    public function onAppProcessResult(ProcessResultEvent $event)
    {
        // code...
    }

    /**
     * @return array
     */
    static function getSubscribedEvents()
    {
        return [
            'on.app.process_result' => 'onAppProcessResult',
        ];
    }
}
