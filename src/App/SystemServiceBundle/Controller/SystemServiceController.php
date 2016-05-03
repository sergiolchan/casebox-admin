<?php

namespace App\SystemServiceBundle\Controller;

use App\DashboardBundle\Service\MessageService;
use App\SystemServiceBundle\Service\SystemServicesService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
    public function indexAction(Request $request)
    {
        $form = $this->setupGetForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $service = $form->getClickedButton()->getName();

            $result = $this->container->get('app_system_service.service.system_services_service')->restartServices(
                $service,
                $data
            );
            
            if (!$result) {
                $this->addFlash('warning', MessageService::SERVICE_NOT_FOUND);
            } else {
                $this->addFlash('success', sprintf(MessageService::SERVICE_RESTARTED, $service));
            }
        }

        $vars['form'] = $form->createView();
        
        return $this->render('AppSystemServiceBundle::index.html.twig', $vars);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function setupGetForm()
    {
        $builder = $this->createFormBuilder([]);

        foreach (SystemServicesService::$services as $name => $command) {
            $builder->add($name, SubmitType::class, [
                    'label' => 'Restart '. ucfirst($name),
                    'attr' => [
                        'class' => 'btn btn-primary form-control',
                        'style' => 'margin-bottom:10px;',
                    ],
                ]
            ); 
        }

        return $builder->getForm();
    }
}
