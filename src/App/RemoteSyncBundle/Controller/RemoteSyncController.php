<?php

namespace App\RemoteSyncBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RemoteSyncController
 */
class RemoteSyncController extends Controller
{
    /**
     * @Route("/admin/sync", name="admin_sync")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('AppRemoteSyncBundle::index.html.twig');
    }

    /**
     * @Route("/admin/sync/database", name="admin_sync_database")
     * @return Response
     */
    public function databaseAction()
    {
        return $this->render('AppRemoteSyncBundle:database:index.html.twig');
    }

    /**
     * @Route("/admin/sync/files", name="admin_sync_files")
     * @return Response
     */
    public function filesAction()
    {
        return $this->render('AppRemoteSyncBundle:files:index.html.twig');
    }
   
}
