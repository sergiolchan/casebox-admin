<?php

namespace App\BackupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackupController
 */
class BackupController extends Controller
{
    /**
     * @Route("/admin/backup", name="admin_backup")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('AppBackupBundle::index.html.twig');
    }

    /**
     * @Route("/admin/backup/database", name="admin_backup_database")
     * @return Response
     */
    public function databaseAction()
    {
        return $this->render('AppBackupBundle:database:index.html.twig');
    }

    /**
     * @Route("/admin/backup/files", name="admin_backup_files")
     * @return Response
     */
    public function filesAction()
    {
        return $this->render('AppBackupBundle:files:index.html.twig');
    }
}
