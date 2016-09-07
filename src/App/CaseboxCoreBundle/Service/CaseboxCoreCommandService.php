<?php

namespace App\CaseboxCoreBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class CaseboxCoreCommandService
 */
class CaseboxCoreCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/casebox.yml';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param array|string|null $params
     *
     * @return mixed
     */
    public function create($params = null)
    {
        $commands['install'] = self::BASE_SCRIPT.' --tags=install --extra-vars="casebox_core='.$params['casebox_core'].' admin_email='.$params['admin_email'].' sender_email='.$params['sender_email'].' root_password='.$params['root_password'].'"';
        
        return $commands;
    }
    
    /**
     * @param array|string|null $params
     *
     * @return mixed
     */
    public function remove($params = null)
    {
        $commands['uninstall'] = self::BASE_SCRIPT.' --tags=uninstall --extra-vars="casebox_core='.$params['casebox_core'].'"';
        
        return $commands;
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
     * @return CaseboxCoreCommandService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
