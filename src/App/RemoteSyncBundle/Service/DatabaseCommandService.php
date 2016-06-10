<?php

namespace App\RemoteSyncBundle\Service;

/**
 * Class DatabaseCommandService
 */
class DatabaseCommandService
{
    /**
     * @param array $params
     *
     * @return array
     */
    public function command(array $params)
    {
        $sshUser = $params['ssh_user'];
        $sshHost = $params['ssh_host'];
        $sshPort = $params['ssh_port'];

        $sqlFile = $params['sql_file'];
        $parFile = $params['parameters_file'];
        $tag = $params['tag'];
        $docroot = (!empty($params['docroot'])) ? $params['docroot'] : '/var/www/casebox';

        $script = 'ansible-playbook -i "'.$sshHost.'," --user='.$sshUser.' /var/provision/ansible/sync/database.yml';

        $commands['sync'] = $script.' --tags='.$tag.' --extra-vars="ansible_ssh_port='.$sshPort.' sql_file=\''.$sqlFile.'\' parameters_file=\''.$parFile.'\' docroot=\''.$docroot.'\'"';

        return $commands;
    }
}
