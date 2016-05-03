<?php

namespace App\SystemLogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SystemLogController
 */
class SystemLogController extends Controller
{
    /**
     * @Route("/admin/logs", name="admin_system_log")
     */
    public function indexAction(Request $request)
    {
        $form = $this->logsGetForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->container->get('app_dashboard.service.redis_service')->del(['queue_logs']);

            return $this->redirectToRoute('admin_system_log');
        }

        $vars = $this->get('app_system_log.service.system_log_service')->getLogs();
        $vars['form'] = $form->createView();
        
        return $this->render('AppSystemLogBundle::index.html.twig', $vars);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function logsGetForm()
    {
        $builder = $this->createFormBuilder([]);
        $builder->add(
            'delete',
            SubmitType::class,
            [
                'label' => 'Clear logs',
                'attr' => [
                    'class' => 'btn btn-warning form-control',
                ],
            ]
        );

        return $builder->getForm();
    }
}
