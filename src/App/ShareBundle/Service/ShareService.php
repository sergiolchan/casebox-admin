<?php

namespace App\ShareBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ShareService
 */
class ShareService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return bool
     */
    public function isShared()
    {
        $fs = new Filesystem();

        $isShared = $this->container->get('app_dashboard.service.redis_service')->get('is_shared');
        if (!$fs->exists('/etc/samba/smb.conf') || empty($isShared)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function install()
    {
        $data['app_share.service.share_command_service']['install'] = [];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($data);

        $this->container->get('app_dashboard.service.redis_service')->set('is_shared', 1);

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
     * @return ShareService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
