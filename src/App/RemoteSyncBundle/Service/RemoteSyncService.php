<?php

namespace App\RemoteSyncBundle\Service;

use App\DashboardBundle\Service\MessageService;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RemoteSyncService
 */
class RemoteSyncService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Request $request
     * @param string  $type
     * @param string  $source
     * @param string  $name
     *
     * @return array
     */
    public function getSyncVars(Request $request, $type, $source, $name = '')
    {
        $vars = [];
        $builder = $this->createFormBuilder([]);

        $choices = [];
        if ($source == 'core') {
            $to = 'remote';
            $from = 'local';
            $choices = $this->container->get('app_remote_sync.service.environment_service')->getHostEnvironments();
        }
        if ($source == 'host') {
            $to = 'local';
            $from = 'remote';
            $choices = $this->container->get('app_remote_sync.service.environment_service')->getCoreEnvironments();
        }

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => sprintf('Synchronise \'%s\' from \'%s\' environment:', $type, $from),
                'data' => $name,
                'required' => true,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );

        $builder->add('_from', HiddenType::class, ['data' => $from]);
        $builder->add('_to', HiddenType::class, ['data' => $to]);
        $builder->add('_type', HiddenType::class, ['data' => $type]);
        $builder->add('_name', HiddenType::class, ['data' => $name]);
        $builder->add('_source', HiddenType::class, ['data' => $source]);

        $builder->add(
            'destination',
            ChoiceType::class,
            [
                'label' => sprintf('Select \'%s\' environment to synchronise:', $to),
                'choices' => $choices,
                'required' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Synchronise',
                'attr' => [
                    'class' => 'btn btn-info form-group form-control',
                ],
            ]
        );

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $destination = explode(':', $data['destination']);
            $data['_destination_source'] = $destination[0];
            $data['_destination_name'] = $destination[1];

            if ($data['_type'] == 'files') {
                $vars = $this->syncFiles($data);
            }

            if ($data['_type'] == 'database') {
                $vars = $this->syncDatabase($data);
            }
        }

        $vars['form'] = $form->createView();

        return $vars;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function syncFiles(array $data)
    {
        $vars = [];

        if ($data['_from'] == 'remote') {
            $match = $this->getSshVars($data['_name']);

            $cmd = [];
            $cmd['app_remote_sync.service.file_sync_command_service']['command'] = [
                'ssh_user' => $match['ssh_user'],
                'ssh_host' => $match['ssh_host'],
                'ssh_port' => (!empty($match['ssh_port'])) ? $match['ssh_port'] : 22,
                // Files
                'terminator' => '/',
                'source' => $match['object']->getDocroot().'/var/files/'.$data['_name'],
                'destination' => '/var/www/casebox/var/files/'.$data['_destination_name'],
                'tag' => 'remote',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);
        }

        if ($data['_from'] == 'local') {
            $match = $this->getSshVars($data['_destination_name']);

            $cmd = [];
            $cmd['app_remote_sync.service.file_sync_command_service']['command'] = [
                'ssh_user' => $match['ssh_user'],
                'ssh_host' => $match['ssh_host'],
                'ssh_port' => (!empty($match['ssh_port'])) ? $match['ssh_port'] : 22,
                // ...
                'terminator' => '/',
                'source' => '/var/www/casebox/var/files/'.$data['_name'],
                'destination' => $match['object']->getDocroot().'/var/files/'.$data['_destination_name'],
                'tag' => 'local',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);
        }
        $vars['message'] = sprintf(MessageService::SYNC_ADD, 2).MessageService::LOGS_VIEW;

        return $vars;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    public function syncDatabase(array $data)
    {
        $dbBackupFile = '/tmp/'.time().'-'.$data['_name'].'.sql';

        if ($data['_from'] == 'remote') {
            $localParamsFile = '/var/www/casebox/app/config/'.$data['_destination_name'].'/parameters.yml';
            $remoteParamsFile = '/var/www/casebox/app/config/'.$data['_name'].'/parameters.yml';

            $match = $this->getSshVars($data['_name']);

            // Backup remote db.
            $cmd = [];
            $cmd['app_remote_sync.service.database_command_service']['command'] = [
                'ssh_user' => $match['ssh_user'],
                'ssh_host' => $match['ssh_host'],
                'ssh_port' => (!empty($match['ssh_port'])) ? $match['ssh_port'] : 22,
                // DB
                'sql_file' => $dbBackupFile,
                'parameters_file' => $remoteParamsFile,
                'tag' => 'backup',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);

            // Sync
            $cmd = [];
            $cmd['app_remote_sync.service.file_sync_command_service']['command'] = [
                'ssh_user' => $match['ssh_user'],
                'ssh_host' => $match['ssh_host'],
                'ssh_port' => (!empty($match['ssh_port'])) ? $match['ssh_port'] : 22,
                // File
                'terminator' => false,
                'source' => $dbBackupFile,
                'destination' => $dbBackupFile,
                'tag' => 'remote',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);

            // Restore db.
            $cmd = [];
            $cmd['app_remote_sync.service.database_command_service']['command'] = [
                'ssh_user' => 'vagrant',
                'ssh_host' => 'localhost',
                'ssh_port' => 22,
                // DB
                'sql_file' => $dbBackupFile,
                'parameters_file' => $localParamsFile,
                'tag' => 'restore',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);
        }

        if ($data['_from'] == 'local') {
            $localParamsFile = '/var/www/casebox/app/config/'.$data['_name'].'/parameters.yml';
            $remoteParamsFile = '/var/www/casebox/app/config/'.$data['_destination_name'].'/parameters.yml';

            // Backup local db.
            $cmd = [];
            $cmd['app_remote_sync.service.database_command_service']['command'] = [
                'ssh_user' => 'vagrant',
                'ssh_host' => 'localhost',
                'ssh_port' => 22,
                // DB
                'sql_file' => $dbBackupFile,
                'parameters_file' => $localParamsFile,
                'tag' => 'backup',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);

            // Sync.
            $match = $this->getSshVars($data['_destination_name']);

            $cmd = [];
            $cmd['app_remote_sync.service.file_sync_command_service']['command'] = [
                'ssh_user' => $match['ssh_user'],
                'ssh_host' => $match['ssh_host'],
                'ssh_port' => (!empty($match['ssh_port'])) ? $match['ssh_port'] : 22,
                // ...
                'terminator' => false,
                'source' => $dbBackupFile,
                'destination' => $dbBackupFile,
                'tag' => 'local',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);

            // Restore db.
            $cmd = [];
            $cmd['app_remote_sync.service.database_command_service']['command'] = [
                'ssh_user' => $match['ssh_user'],
                'ssh_host' => $match['ssh_host'],
                'ssh_port' => (!empty($match['ssh_port'])) ? $match['ssh_port'] : 22,
                // DB
                'sql_file' => $dbBackupFile,
                'parameters_file' => $remoteParamsFile,
                'docroot' => $match['object']->getDocroot(),
                'tag' => 'restore',
            ];
            $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);
        }

        $vars['message'] = sprintf(MessageService::SYNC_ADD, 2).MessageService::LOGS_VIEW;

        return $vars;
    }

    /**
     * @param string $env
     *
     * @return array
     * @throws \Exception
     */
    public function getSshVars($env)
    {
        $obj = $this->container->get('app_remote_sync.service.host_service')->getHostByEnvironment($env);
        preg_match('/([^@]*)@([^:]*):([^$]*)/is', $obj->getAddress(), $match);

        $vars = [
            'object' => $obj,
            'ssh_user' => $match[1],
            'ssh_host' => $match[2],
            'ssh_port' => (!empty($match[3])) ? $match[3] : 22,
        ];

        return $vars;
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
     * @return RemoteSyncService $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Creates and returns a form builder instance.
     *
     * @param mixed $data    The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormBuilder
     */
    protected function createFormBuilder($data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createBuilder(FormType::class, $data, $options);
    }
}
