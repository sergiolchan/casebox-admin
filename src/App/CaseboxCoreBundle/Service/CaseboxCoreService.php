<?php

namespace App\CaseboxCoreBundle\Service;

use App\CaseboxCoreBundle\Entity\Core;
use App\CaseboxCoreBundle\Event\CaseboxCoreEvent;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class CaseboxCoreService
 */
class CaseboxCoreService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return Core
     * @throws \Exception
     */
    public function getAllCores()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $cores = $em->getRepository('AppCaseboxCoreBundle:Core')->findAll();

        return $cores;
    }
    
    /**
     * @param string $coreName
     *
     * @return Core
     * @throws \Exception
     */
    public function getCoreByCoreName($coreName)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $core = $em->getRepository('AppCaseboxCoreBundle:Core')->findOneBy(['coreName' => $coreName]);

        return $core;
    }

    /**
     * @param array $data
     *
     * @return Core
     */
    public function addCore(array $data)
    {
        // Validate $data['coreName']
        if ($this->getCoreByCoreName($data['coreName']) instanceof Core) {
            $this->container->get('session')->getFlashBag()->add(
                'warning',
                sprintf('Casebox core %s already exist.', $data['coreName'])
            );

            return $this->getCoreByCoreName($data['coreName']);
        }

        $core = new Core();
        $core->setCoreName($data['coreName']);
        $core->setAdminEmail($data['adminEmail']);
        $core->setSenderEmail($data['senderEmail']);

        $this->container->get('doctrine.orm.entity_manager')->persist($core);
        $this->container->get('doctrine.orm.entity_manager')->flush();
        
        // Dispatch
        $this->container->get('event_dispatcher')->dispatch('on.app.casebox_core.create', new CaseboxCoreEvent($core));

        // Write create queue
        $data = [
            'app_casebox_core.service.casebox_core_command_service' => [
                'create' => [
                    'create' => [
                        'coreName' => $core->getCoreName(),
                        'adminEmail' => $core->getAdminEmail(),
                        'senderEmail' => $core->getSenderEmail(),
                    ],
                ],
            ],
        ];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        return $core;
    }

    /**
     * @param Core $core
     *
     * @return Core
     */
    public function editCore(Core $core)
    {
        $this->container->get('doctrine.orm.entity_manager')->flush($core);

        // Dispatch
        $this->container->get('event_dispatcher')->dispatch('on.app.casebox_core.edit', new CaseboxCoreEvent($core));

        return $core;
    }

    /**
     * @param Core $core
     *
     * @return Core
     */
    public function deleteCore(Core $core)
    {
        // Dispatch
        $this->container->get('event_dispatcher')->dispatch('on.app.casebox_core.remove', new CaseboxCoreEvent($core));

        // Write create queue
        $data = [
            'app_casebox_core.service.casebox_core_command_service' => [
                'remove' => [
                    'remove' => [
                        'coreName' => $core->getCoreName(),
                        'adminEmail' => $core->getAdminEmail(),
                        'senderEmail' => $core->getSenderEmail(),
                    ],
                ],
            ],
        ];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        $this->container->get('doctrine.orm.entity_manager')->remove($core);
        $this->container->get('doctrine.orm.entity_manager')->flush();

        return true;
    }

    /**
     * @param Core $core
     *
     * @return Core
     */
    public function getActionsHtml(Core $core)
    {
        $actions[] = '<a href="/admin/core/'.$core->getId().'/delete">Delete</a>';

        return implode('', $actions);
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
     * @return CaseboxCoreService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
