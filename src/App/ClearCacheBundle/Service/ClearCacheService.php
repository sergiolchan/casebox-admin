<?php

namespace App\ClearCacheBundle\Service;

use App\CaseboxCoreBundle\Entity\Core;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ClearCacheService
 */
class ClearCacheService
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
    public function clear(Core $core)
    {
        $core->setStatus(Core::STATUS_WORKING);
        $this->container->get('app_casebox_core.service.casebox_core_service')->editCore($core);

        // Clear cache
        $data['app_clear_cache.service.clear_cache_command_service']['clear'] = ['casebox_core' => $core->getCoreName()];
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
     * @return ClearCacheService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
