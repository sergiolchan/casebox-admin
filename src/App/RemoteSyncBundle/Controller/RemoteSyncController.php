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
                    'class' => 'form-control',
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
                    'class' => 'form-control',
                ],
            ]
        );
        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Next',
                'attr' => [
                    'class' => 'btn btn-info form-control',
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

            return $this->redirectToRoute('admin_sync_'.$type, ['source' => $source[0], 'name' => $source[1]]);
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
     * @Route("/admin/sync/database/{source}/{name}", name="admin_sync_database")
     * @param Request $request
     * @param string  $source
     * @param string  $name
     *
     * @return Response
     */
    public function databaseAction(Request $request, $source, $name)
    {
        $vars = $this->getSyncVars($request, $source, $name);

        return $this->render('AppRemoteSyncBundle:database:index.html.twig', $vars);
    }

    /**
     * @Route("/admin/sync/files/{source}/{name}", name="admin_sync_files")
     * @param Request $request
     * @param string  $source
     * @param string  $name
     *
     * @return Response
     */
    public function filesAction(Request $request, $source, $name)
    {
        $vars = $this->getSyncVars($request, $source, $name);

        return $this->render('AppRemoteSyncBundle:files:index.html.twig', $vars);
    }

    /**
     * @param Request $request
     * @param string  $source
     * @param string  $name
     *
     * @return array
     */
    protected function getSyncVars(Request $request, $source, $name = '')
    {
        $builder = $this->createFormBuilder([]);

        $choices = [];
        if ($source == 'core') {
            $type = 'remote';
            $choices = $this->container->get('app_remote_sync.service.environment_service')->getHostEnvironments();
        }
        if ($source == 'host') {
            $type = 'local';
            $choices = $this->container->get('app_remote_sync.service.environment_service')->getCoreEnvironments();
        }

        $builder->add('_source', HiddenType::class, array(
            'data' => $source,
        ));
        $builder->add('_name', HiddenType::class, array(
            'data' => $name,
        ));

        $builder->add(
            'source',
            ChoiceType::class,
            [
                'label' => sprintf('Select %s environment to synchronise.', $type),
                'choices' => $choices,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Synchronise',
                'attr' => [
                    'class' => 'btn btn-info form-control',
                ],
            ]
        );

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // code...
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
