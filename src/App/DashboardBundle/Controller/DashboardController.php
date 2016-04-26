<?php

namespace App\DashboardBundle\Controller;

use App\CaseboxCoreBundle\Entity\Core;
use App\DashboardBundle\Traits\DateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 */
class DashboardController extends Controller
{
    use DateTrait;

    /**
     * @Route("/", name="admin")
     * @return Response
     */
    public function indexAction()
    {
        $vars['items'] = [];
        $cores = $this->get('app_casebox_core.service.casebox_core_service')->getAllCores();

        if (empty($cores)) {
            return $this->render('AppDashboardBundle:dashboard:startup.html.twig', $vars);
        }

        foreach ($cores as $core) {
            if ($core instanceof Core) {
                $vars['items'][] = [
                    'id' => $core->getId(),
                    'coreName' => $core->getCoreName(),
                    'adminEmail' => $core->getAdminEmail(),
                    'createdAt' => $this->formatDate($core->getCreateAt()),
                    'actions' => $this->get('app_casebox_core.service.casebox_core_service')->getActionsHtml($core),
                ];
            }
        }
        
        return $this->render('AppDashboardBundle:dashboard:index.html.twig', $vars);
    }

    /**
     * @Route("/admin/rsa", name="admin_rsa")
     * @return Response
     */
    public function rsaKeyAction()
    {
        return $this->render('AppDashboardBundle:rsa:index.html.twig');
    }
}
