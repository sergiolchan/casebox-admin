<?php

namespace App\ComposerBundle\Service;

/**
 * Class ComposerUpdateCommandService
 */
class ComposerUpdateCommandService
{
    const BASE_SCRIPT = 'ansible-playbook -i "localhost," -c local /var/provision/ansible/composer.yml';

    /**
     * @param array $params
     *
     * @return array
     */
    public function update(array $params = [])
    {
        $commands['update'] = self::BASE_SCRIPT.' --tags=update --extra-vars="casebox_core='.$params['casebox_core'].'"';

        return $commands;
    }
}
