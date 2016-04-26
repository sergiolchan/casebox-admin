<?php

namespace App\SystemServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SystemServiceController
 */
class SystemServiceController extends Controller
{
    /**
     * @Route("/admin/services", name="admin_system_service")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('AppSystemServiceBundle::index.html.twig');
    }
}
