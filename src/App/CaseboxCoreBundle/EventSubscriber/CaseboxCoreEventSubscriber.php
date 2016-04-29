<?php

namespace App\CaseboxCoreBundle\EventSubscriber;

use App\CaseboxCoreBundle\Event\CaseboxCoreEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CaseboxCoreEventSubscriber
 */
class CaseboxCoreEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param CaseboxCoreEvent $event
     */
    public function onCaseboxCoreCreate(CaseboxCoreEvent $event)
    {
        // code...
    }
    
    /**
     * @param CaseboxCoreEvent $event
     */
    public function onCaseboxCoreEdit(CaseboxCoreEvent $event)
    {
        // code...
    }
    
    /**
     * @param CaseboxCoreEvent $event
     */
    public function onCaseboxCoreRemove(CaseboxCoreEvent $event)
    {
        // code...
    }

    /**
     * @return array
     */
    static function getSubscribedEvents()
    {
        return [
            'on.app.casebox_core.create' => 'onCaseboxCoreCreate',
            'on.app.casebox_core.edit' => 'onCaseboxCoreEdit',
            'on.app.casebox_core.remove' => 'onCaseboxCoreRemove',
        ];
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     *
     * @return CaseboxCoreEventSubscriber $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
