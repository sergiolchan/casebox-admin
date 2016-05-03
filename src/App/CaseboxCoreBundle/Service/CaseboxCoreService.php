<?php

namespace App\CaseboxCoreBundle\Service;

use App\CaseboxCoreBundle\Entity\Core;
use App\CaseboxCoreBundle\Event\CaseboxCoreEvent;
use App\DashboardBundle\Event\ProcessResultEvent;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;
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
        $cores = $this->container->get('app_casebox_core.repository.core_repository')->find();

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
        $core = $this->container->get('app_casebox_core.repository.core_repository')->findOne(
            ['coreName' => $coreName]
        );

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
        $core->setStatus(Core::STATUS_PENDING);

        $this->container->get('app_casebox_core.repository.core_repository')->save($core);

        // Dispatch
        $this->container->get('event_dispatcher')->dispatch('on.app.casebox_core.create', new CaseboxCoreEvent($core));

        // Write command to queue
        $data['app_casebox_core.service.casebox_core_command_service']['create'] = ['casebox_core' => $core->getCoreName()];
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
        $core->setUpdatedAt(time());

        $this->container->get('app_casebox_core.repository.core_repository')->save($core);

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

        // Command
        $data['app_casebox_core.service.casebox_core_command_service']['remove'] = [
            'casebox_core' => $core->getCoreName(),
        ];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        // Delete
        $this->container->get('app_casebox_core.repository.core_repository')->delete($core);

        return true;
    }

    public function logging(Logger $logger)
    {
        // code...
    }

    /**
     * Update object status.
     *
     * @param ProcessResultEvent $event
     */
    public function onAppProcessResult(ProcessResultEvent $event)
    {
        $params = $event->getParams();

        // Install
        if (!empty($params['app_casebox_core.service.casebox_core_command_service'])) {
            foreach ($params['app_casebox_core.service.casebox_core_command_service'] as $method => $values) {
                if ($method == 'create') {
                    // @todo - Code refactoring. Catch ansible (provision) warnings.
                    $status = Core::STATUS_DONE;
                    $core = $this->getCoreByCoreName(trim($values['params']['casebox_core']));
                    if ($core instanceof Core) {
                        $core->setStatus($status);
                        // Update
                        $this->editCore($core);
                    }
                }
            }
        }

        // Composer update
        if (!empty($params['app_composer.service.composer_update_command_service'])) {
            foreach ($params['app_composer.service.composer_update_command_service'] as $method => $values) {
                if ($method == 'update') {
                    $coreName = $values['params']['casebox_core'];

                    if (!empty($values['process']['err'])) {
                        foreach ($values['process']['err'] as $key => $value) {
                            if (strstr($value, 'WARNING') || empty($value)) {
                                unset($values['process']['err'][$key]);
                            }
                        }
                    }

                    $status = Core::STATUS_ERROR;

                    if (empty($values['process']['err'])) {
                        $status = Core::STATUS_DONE;
                    }

                    $core = $this->getCoreByCoreName($coreName);
                    if ($core instanceof Core) {
                        $core->setStatus($status);

                        // Update
                        $this->editCore($core);
                    }
                }
            }
        }
    }

    /**
     * @param Core $core
     *
     * @return Core
     */
    public function getActionsHtml(Core $core)
    {
        $type = 'success';

        if (empty($core->getStatus())) {
            $status = 'N/A';
        } else {
            $status = $core->getStatus();

            if ($status == Core::STATUS_ERROR) {
                $type = 'warning';
            }

            if ($status == Core::STATUS_WORKING) {
                $type = 'info';
            }
        }

        $actions[] = '<span class="text-muted text-'.$type.'">'.$status.'</span>';

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
