<?php

namespace App\DashboardBundle\Service;

use App\DashboardBundle\Entity\Registry;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class RegistryService
 */
class RegistryService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $name Registry key name
     * @param array|int|null|string $data Default value
     *
     * @return array|int|null|string
     */
    public function get($name, $data = null)
    {
        $value = $data;

        $registry = $this->registry()->findOneBy(['name' => $name]);

        if ($registry instanceof Registry) {
            $value = $registry->getData();
        }

        return $value;
    }

    /**
     * @param string $name
     * @param array|int|null|string $data
     *
     * @return array|int|null|string
     * @throws \Exception
     */
    public function set($name, $data)
    {
        $registry = $this->registry()->findOneBy(['name' => $name]);
        if ($registry instanceof Registry) {
            $this->remove($name);
        }

        $registry = new Registry();
        $registry->setName($name);
        $registry->setData($data);

        $this->container->get('doctrine.orm.entity_manager')->persist($registry);
        $this->container->get('doctrine.orm.entity_manager')->flush();

        return $data;
    }

    /**
     * @param string $name
     *
     * @return bool
     * @throws \Exception
     */
    public function remove($name)
    {
        $registry = $this->registry()->findOneBy(['name' => $name]);
        if ($registry instanceof Registry) {
            $this->container->get('doctrine.orm.entity_manager')->remove($registry);
            $this->container->get('doctrine.orm.entity_manager')->flush();

            return true;
        }

        return false;
    }

    protected function registry()
    {
        $registry = $this->container->get('doctrine.orm.entity_manager')->getRepository('AppDashboardBundle:Registry');

        return $registry;
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
     * @return RegistryService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
