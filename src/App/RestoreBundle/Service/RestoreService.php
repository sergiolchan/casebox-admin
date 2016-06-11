<?php

namespace App\RestoreBundle\Service;

use App\DashboardBundle\Service\MessageService;
use App\DashboardBundle\Traits\StringTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class RestoreService
 */
class RestoreService
{
    use StringTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @return array
     */
    public function getBackups()
    {
        $items = [];

        $cbParams = $this->container->getParameter('casebox');
        $files = array_diff(scandir($cbParams['backup_dir']), ['.', '..']);

        if (!empty($files)) {
            foreach ($files as $filename) {
                $id = substr(md5($filename), 0, 10);
                $filePath = $cbParams['backup_dir'].'/'.$filename;

                $items[$id] = [
                    'id' => $id,
                    'file' => $filename,
                    'file_path' => $filePath,
                    'size' => $this->humanFileSize(filesize($filePath)),
                ];
            }
        }

        return $items;
    }

    /**
     * @param string $id
     *
     * @return array|void
     */
    public function getRestoreDestination($id)
    {
        $items = $this->getBackups();

        if (empty($items[$id])) {
            return;
        }

        $builder = $this->container->get('form.factory')->createBuilder(FormType::class, [], []);

        // Form
        $choices = $this->container->get('app_remote_sync.service.environment_service')->getCoreEnvironments();
        $builder->add(
            'destination',
            ChoiceType::class,
            [
                'label' => 'Select \'local\' environment to restore:',
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
                'label' => 'Restore',
                'attr' => [
                    'class' => 'btn btn-info form-group form-control',
                ],
            ]
        );

        $form = $builder->getForm();

        $vars = [
            'item' => $items[$id],
            'form' => $form,
        ];

        return $vars;
    }

    /**
     * @param array  $source
     * @param string $destination
     *
     * @return array
     * @throws \Exception
     */
    public function restore(array $source, $destination)
    {
        $destination = explode(':', $destination);
        $localParamsFile = '/var/www/casebox/app/config/'.$destination[1].'/parameters.yml';

        // Restore db.
        $cmd = [];
        $cmd['app_remote_sync.service.database_command_service']['command'] = [
            'ssh_user' => 'vagrant',
            'ssh_host' => 'localhost',
            'ssh_port' => 22,
            // DB
            'sql_file' => $source['file_path'],
            'parameters_file' => $localParamsFile,
            'tag' => 'restore',
        ];
        $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);

        $vars['message'] = sprintf(MessageService::SYNC_ADD, 2).MessageService::LOGS_VIEW;

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
     * @return RestoreService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
