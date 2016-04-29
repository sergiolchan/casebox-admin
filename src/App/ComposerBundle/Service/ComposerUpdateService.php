<?php

namespace App\ComposerBundle\Service;

use App\CaseboxCoreBundle\Entity\Core;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ComposerUpdateService
 */
class ComposerUpdateService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Core $core
     *
     * @return bool
     * @throws \Exception
     */
    public function update(Core $core)
    {
        // Composer update
        $data['app_composer.service.composer_update_command_service']['update'] = ['casebox_core' => $core->getCoreName()];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        return true;
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
     * @return ComposerUpdateService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
