<?php

namespace App\CaseboxCoreBundle\Controller;

use App\CaseboxCoreBundle\Entity\Core;
use App\CaseboxCoreBundle\Form\CoreType;
use App\DashboardBundle\Traits\DateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CaseboxCoreController
 */
class CaseboxCoreController extends Controller
{
    use DateTrait;
    
    /**
     * @Route("/admin/core", name="admin_core")
     * @return Response
     */
    public function indexAction()
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
                    'actions' => $this->get('app_casebox_core.service.casebox_core_service')->getActionsHtml($core),
                ];
            }
        }

        return $this->render('AppCaseboxCoreBundle::index.html.twig', $vars);
    }

    /**
     * @Route("/admin/core/add", name="admin_core_add")
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(CoreType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app_casebox_core.service.casebox_core_service')->addContainer($form->getData());

            return $this->redirectToRoute('admin_core');
        }

        $vars = [
            'form' => $form->createView(),
        ];

        return $this->render('AppCaseboxCoreBundle::add.html.twig', $vars);
    }

    /**
     * @Route("/admin/core/{id}/view", name="admin_core_view")
     * @Route("/admin/core/{id}/delete", name="admin_core_delete")
     * @param Request $request
     *
     * @return Response
     */
    public function viewAction(Request $request, Core $core)
    {
        $builder = $this->createFormBuilder([]);
        $builder->add(
            'delete',
            SubmitType::class,
            [
                'label' => 'Delete',
                'attr' => [
                    'class' => 'btn btn-danger form-control',
                ],
            ]
        );
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app_casebox_core.service.casebox_core_service')->deleteContainer($core);

            return $this->redirectToRoute('admin_core');
        }

        $url = $this->container->getParameter('server_name').'/c/'.$core->getCoreName();

        $vars = [
            'url' => sprintf('<a href="%s" target="_blank">%s</a>', $url, $url),
            'coreName' => $core->getCoreName(),
            'adminEmail' => $core->getAdminEmail(),
            'senderEmail' => $core->getSenderEmail(),
            'createdAt' => $this->formatDate($core->getCreateAt()),
            'form' => $form->createView(),
        ];

        return $this->render('AppCaseboxCoreBundle::edit.html.twig', $vars);
    }
}
