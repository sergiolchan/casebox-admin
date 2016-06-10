<?php

namespace App\RemoteSyncBundle\Controller;

use App\DashboardBundle\Service\MessageService;
use App\RemoteSyncBundle\Entity\Host;
use App\RemoteSyncBundle\Form\HostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RemoteSyncController
 */
class RemoteSyncController extends Controller
{
    const SOURCE_DATABASE = 'database';
    const SOURCE_FILES    = 'files';

    /**
     * @Route("/admin/sync", name="admin_sync")
     * @return Response
     */
    public function indexAction()
    {
        $vars['items'] = [];
        $hosts = $this->get('app_remote_sync.repository.host_repository')->find();

        foreach ($hosts as $host) {
            if ($host instanceof Host) {
                $vars['items'][] = [
                    'id' => $host->getId(),
                    'address' => $host->getAddress(),
                    'environment' => $host->getEnvironment(),
                    'docroot' => $host->getDocroot(),
                    'description' => $host->getDescription(),
                ];
            }
        }

        return $this->render('AppRemoteSyncBundle::index.html.twig', $vars);
    }

    /**
     * @Route("/admin/sync/source", name="admin_sync_source")
     * @param Request $request
     *
     * @return Response
     */
    public function sourceAction(Request $request)
    {
        $type = $request->get('type');

        $builder = $this->createFormBuilder([]);
        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'Select what to synchronise?',
                'choices' => [
                    'database' => 'database',
                    'files' => 'files',
                ],
                'data' => (!empty($type)) ? $type : 'database',
                'required' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );
        $builder->add(
            'source',
            ChoiceType::class,
            [
                'label' => 'Select synchronisation source (from).',
                'choices' => $this->container->get('app_remote_sync.service.environment_service')->getAllEnvironments(),
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
                'label' => 'Next',
                'attr' => [
                    'class' => 'btn btn-info form-group form-control',
                ],
            ]
        );

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];

            $types = [self::SOURCE_DATABASE, self::SOURCE_FILES];
            if (empty($type) || !in_array($type, $types) || empty($data['source'])) {
                return $this->sourceNotFound(MessageService::SYNC_SOURCE_NOT_SOUND, $request->query->all());
            }

            $source = explode(':', $data['source']);
            if (!is_array($source)) {
                return $this->sourceNotFound(MessageService::SYNC_SOURCE_NOT_SOUND, $request->query->all());
            }

            return $this->redirectToRoute(
                'admin_sync_sync',
                ['type' => $type, 'source' => $source[0], 'name' => $source[1]]
            );
        }

        $vars = [
            'form' => $form->createView(),
        ];

        return $this->render('AppRemoteSyncBundle::source.html.twig', $vars);
    }

    /**
     * @Route("/admin/host/add", name="admin_host_add")
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(HostType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $host = $this->container->get('app_remote_sync.service.host_service')->getHostByAddress($data['address']);
            if ($host instanceof Host) {
                $this->addFlash('warning', sprintf(MessageService::HOST_ADD_FAIL, $data['address']));

                return $this->redirectToRoute('admin_sync');
            }
            $this->get('app_remote_sync.service.host_service')->addHost($data);
            $this->addFlash('success', sprintf(MessageService::HOST_ADD));

            return $this->redirectToRoute('admin_sync');
        }

        $vars = [
            'form' => $form->createView(),
        ];

        return $this->render('AppRemoteSyncBundle::add.html.twig', $vars);
    }

    /**
     * @Route("/admin/host/{id}/view", name="admin_host_view")
     * @Route("/admin/host/{id}/delete", name="admin_host_delete")
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function viewAction(Request $request, $id)
    {
        /** @var Host $host */
        $host = $this->get('app_remote_sync.repository.host_repository')->findOne(['id' => $id]);

        $builder = $this->createFormBuilder([]);
        $builder->add(
            'delete',
            SubmitType::class,
            [
                'label' => 'Delete',
                'attr' => [
                    'class' => 'btn btn-danger form-control',
                ],
            ]
        );

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app_remote_sync.service.host_service')->deleteHost($host);

            return $this->redirectToRoute('admin_sync');
        }

        $vars = [
            'address' => $host->getAddress(),
            'docroot' => $host->getDocroot(),
            'environment' => $host->getEnvironment(),
            'form' => $form->createView(),
        ];

        return $this->render('AppRemoteSyncBundle::edit.html.twig', $vars);
    }

    /**
     * @Route("/admin/sync/{type}/{source}/{name}", name="admin_sync_sync")
     * @param Request $request
     * @param string  $type
     * @param string  $source
     * @param string  $name
     *
     * @return Response
     */
    public function syncAction(Request $request, $type, $source, $name)
    {
        $vars = $this->getSyncVars($request, $type, $source, $name);

        return $this->render('AppRemoteSyncBundle::sync.html.twig', $vars);
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param string  $source
     * @param string  $name
     *
     * @return array
     */
    protected function getSyncVars(Request $request, $type, $source, $name = '')
    {
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
                'label' => sprintf('Sync %s from %s source', $type, $from),
                'data' => $name,
                'required' => true,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );

        $builder->add(
            '_from',
            HiddenType::class,
            [
                'data' => $from,
            ]
        );
        $builder->add(
            '_to',
            HiddenType::class,
            [
                'data' => $to,
            ]
        );
        $builder->add(
            '_type',
            HiddenType::class,
            [
                'data' => $type,
            ]
        );
        $builder->add(
            '_name',
            HiddenType::class,
            [
                'data' => $name,
            ]
        );
        $builder->add(
            '_source',
            HiddenType::class,
            [
                'data' => $source,
            ]
        );

        $builder->add(
            'destination',
            ChoiceType::class,
            [
                'label' => sprintf('Select %s environment to synchronise.', $to),
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

            $sshDefault = [
                'ssh_user' => 'vagrant',
                'ssh_host' => 'localhost',
                'ssh_port' => 22,
            ];

            if ($data['_from'] == 'local') {
                $sshFrom = $sshDefault;
            } elseif ($data['_from'] == 'remote' && $data['_source'] == 'host') {
                $obj = $this->container->get('app_remote_sync.service.host_service')->getHostByEnvironment(
                    $data['_name']
                );
                preg_match('/([^@]*)@([^:]*):([^$]*)/is', $obj->getAddress(), $match);
                $sshFrom = [
                    'ssh_user' => $match[1],
                    'ssh_host' => $match[2],
                    'ssh_port' => (!empty($match[3])) ? $match[3] : 22,
                ];
            }

            if ($data['_type'] == 'files') {
                // code...
            }

            if ($data['_type'] == 'database') {
                if ($data['_from'] == 'local') {
                    $localParamsFile = '/var/www/casebox/app/config/'.$data['_name'].'/parameters.yml';
                    $remoteParamsFile = '/var/www/casebox/app/config/'.$data['_destination_name'].'/parameters.yml';

                    $dbBackupFile = '/tmp/'.time().'-'.$data['_name'].'.sql';

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
                    $obj = $this->container->get('app_remote_sync.service.host_service')->getHostByEnvironment(
                        $data['_destination_name']
                    );
                    preg_match('/([^@]*)@([^:]*):([^$]*)/is', $obj->getAddress(), $match);

                    $cmd = [];
                    $cmd['app_remote_sync.service.file_sync_command_service']['command'] = [
                        'ssh_user' => $match[1],
                        'ssh_host' => $match[2],
                        'ssh_port' => (!empty($match[3])) ? $match[3] : 22,
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
                        'ssh_user' => $match[1],
                        'ssh_host' => $match[2],
                        'ssh_port' => (!empty($match[3])) ? $match[3] : 22,
                        // DB
                        'sql_file' => $dbBackupFile,
                        'parameters_file' => $remoteParamsFile,
                        'docroot' => $obj->getDocroot(),
                        'tag' => 'restore',
                    ];
                    $this->container->get('app_dashboard.service.queue_service')->queueWrite($cmd);

                    $message = sprintf(MessageService::SYNC_ADD, 2).MessageService::LOGS_VIEW;
                    $this->addFlash('success', $message);
                }
            }
        }

        return $vars = [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param string $message
     * @param array  $query
     *
     * @return RedirectResponse
     */
    protected function sourceNotFound($message = MessageService::SYNC_SOURCE_NOT_SOUND, array $query = [])
    {
        $this->addFlash('warning', $message);

        return $this->redirectToRoute('admin_sync_source', $query);
    }
}
