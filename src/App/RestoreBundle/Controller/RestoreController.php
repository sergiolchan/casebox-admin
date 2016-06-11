<?php

namespace App\RestoreBundle\Controller;

use App\DashboardBundle\Service\MessageService;
use Ratchet\WebSocket\Version\RFC6455\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RestoreController
 */
class RestoreController extends Controller
{
    /**
     * @Route("/admin/restore", name="admin_restore")
     * @return Response
     */
    public function indexAction()
    {
        $vars['items'] = $this->get('app_restore.service.restore_service')->getBackups();

        return $this->render('AppRestoreBundle::index.html.twig', $vars);
    }

    /**
     * @Route("/admin/restore/database", name="admin_restore_database")
     * @param Request $request
     *
     * @return Response
     */
    public function databaseAction(Request $request)
    {
        $id = $request->get('id');

        if (empty($id)) {
            $this->addFlash('warning', MessageService::RESTORE_SOURCE_NOT_SOUND);

            return $this->redirectToRoute('admin_restore');
        }

        $vars = $this->get('app_restore.service.restore_service')->getRestoreDestination($id);

        if (empty($vars)) {
            $this->addFlash('warning', MessageService::RESTORE_SOURCE_NOT_SOUND);

            return $this->redirectToRoute('admin_restore');
        }

        /** @var Form $form */
        $form = $vars['form'];
        $source = $vars['item'];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $result = $this->get('app_restore.service.restore_service')->restore($source, $data['destination']);
            $this->addFlash('success', $result['message']);

            return $this->redirectToRoute('admin_restore');
        }

        $vars['form'] = $form->createView();

        return $this->render('AppRestoreBundle::database.html.twig', $vars);
    }
}
