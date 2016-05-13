<?php

namespace App\ShareBundle\Controller;

use App\DashboardBundle\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ShareController
 */
class ShareController extends Controller
{
    /**
     * @Route("/admin/share", name="admin_share")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $isShared = $this->container->get('app_share.service.share_service')->isShared();

        if ($isShared) {
            return $this->render('AppShareBundle::shared.html.twig');
        }

        $form = $this->installForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app_share.service.share_service')->install();
            $this->addFlash('success', MessageService::SHARE_SUCCESS.' '.MessageService::LOGS_VIEW);

            return $this->redirectToRoute('admin_share');
        }

        $vars['form'] = $form->createView();

        return $this->render('AppShareBundle::index.html.twig', $vars);
    }

    /**
     * @return Form
     */
    public function installForm()
    {
        $builder = $this->createFormBuilder([]);
        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Install SAMBA and share casebox',
                'attr' => [
                    'class' => 'btn btn-info form-control',
                ],
            ]
        );

        return $builder->getForm();
    }
}
