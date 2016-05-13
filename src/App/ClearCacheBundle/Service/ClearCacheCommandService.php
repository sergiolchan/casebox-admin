<?php

namespace App\ClearCacheBundle\Service;

/**
 * Class ClearCacheCommandService
 */
class ClearCacheCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/cacheclear.yml';

    /**
     * @param array $params
     *
     * @return array
     */
    public function clear(array $params = [])
    {
        $commands['clear'] = self::BASE_SCRIPT.' --extra-vars="casebox_core='.$params['casebox_core'].'"';

        return $commands;
    }
}
