<?php

namespace App\DashboardBundle\Controller;

use App\CaseboxCoreBundle\Entity\Core;
use App\DashboardBundle\Service\MessageService;
use App\DashboardBundle\Traits\DateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
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
    public function indexAction(Request $request)
    {
        $vars['items'] = [];
        $cores = $this->get('app_casebox_core.service.casebox_core_service')->getAllCores();
        
        foreach ($cores as $core) {
            if ($core instanceof Core) {
                $vars['items'][] = [
                    'id' => $core->getId(),
                    'coreName' => $core->getCoreName(),
                    'adminEmail' => $core->getAdminEmail(),
                    'createdAt' => $this->formatDate($core->getCreateAt()),
                    'updatedAt' => (!empty($core->getUpdatedAt())) ? $this->formatDate($core->getUpdatedAt()) : 'N/A',
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
        $vars = $this->container->get('app_dashboard.service.rsa_key_service')->getKeys();

        return $this->render('AppDashboardBundle:rsa:index.html.twig', $vars);
    }
}
