<?php

namespace App\SystemLogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class SystemLogController
 */
class SystemLogController extends Controller
{
    /**
     * @Route("/admin/logs", name="admin_system_log")
     */
    public function indexAction()
    {
        return $this->render('AppSystemLogBundle::index.html.twig');
    }
}
