<?php

namespace App\CaseboxCoreBundle\Service;

use App\CaseboxCoreBundle\Entity\Core;
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
    public function addContainer(array $data)
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

        return $core;
    }

    /**
     * @param Core $core
     *
     * @return Core
     */
    public function editContainer(Core $core)
    {
        $this->container->get('doctrine.orm.entity_manager')->flush($core);

        return $core;
    }

    /**
     * @param Core $core
     *
     * @return Core
     */
    public function deleteContainer(Core $core)
    {
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
