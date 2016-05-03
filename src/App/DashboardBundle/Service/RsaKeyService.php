<?php

namespace App\DashboardBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class RsaKeyService
 */
class RsaKeyService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return array
     */
    public function getKeys()
    {
        $cache = $this->container->get('app_dashboard.service.redis_service')->get('rsa_cache');
        if (empty($cache)) {
            $result = [
                'vagrant' => file_get_contents('/home/vagrant/.ssh/id_rsa.pub'),
                'root' => file_get_contents('/root/.ssh/id_rsa.pub'),
            ];

            $cache = json_encode($result);
            $this->container->get('app_dashboard.service.redis_service')->set('rsa_cache', $cache, 60 * 60);
        } else {
            $result = json_decode($cache, true);
        }

        return $result;
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
     * @return RsaKeyService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
