<?php

namespace App\CaseboxCoreBundle\Controller;

use App\CaseboxCoreBundle\Entity\Core;
use App\CaseboxCoreBundle\Form\CoreType;
use App\DashboardBundle\Service\MessageService;
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
    const BTN_UPDATE = 'update';
    const BTN_DELETE = 'delete';

    use DateTrait;
    
    /**
     * @Route("/admin/core", name="admin_core")
     * @return Response
     */
    public function indexAction()
    {
        $vars['items'] = [];
        $cores = $this->get('app_casebox_core.repository.core_repository')->find();

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
            $this->get('app_casebox_core.service.casebox_core_service')->addCore($form->getData());

            $message = sprintf(MessageService::CORE_ADD, 2);
            
            $cores = $this->get('app_casebox_core.repository.core_repository')->find();
            if (empty($cores)) {
                $message = sprintf(MessageService::CORE_FIRST_TIME_ADD, 7);
            }

            $message .= MessageService::LOGS_VIEW;

            $this->addFlash('warning', $message);

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
    public function viewAction(Request $request, $id)
    {
        $core = $this->get('app_casebox_core.repository.core_repository')->findOne(['id' => $id]);

        $builder = $this->createFormBuilder([]);
        $builder->add(
            self::BTN_UPDATE,
            SubmitType::class,
            [
                'label' => 'Composer update',
                'attr' => [
                    'class' => 'btn btn-info',
                ],
            ]
        );
        
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($form->getClickedButton()->getName()) {
                case self::BTN_UPDATE:
                    $this->get('app_composer.service.composer_update_service')->update($core);

                    break;

                case  self::BTN_DELETE:
                    $this->get('app_casebox_core.service.casebox_core_service')->deleteCore($core);

                    break;
            }

            return $this->redirectToRoute('admin_core');
        }

        $url = $this->container->getParameter('server_name').'/c/'.$core->getCoreName();

        $vars = [
            'url' => sprintf('<a href="%s" target="_blank">%s</a>', $url, $url),
            'coreName' => $core->getCoreName(),
            'adminEmail' => $core->getAdminEmail(),
            'senderEmail' => $core->getSenderEmail(),
            'createdAt' => $this->formatDate($core->getCreateAt()),
            'updatedAt' => (!empty($core->getUpdatedAt())) ? $this->formatDate($core->getUpdatedAt()) : 'N/A',
            'form' => $form->createView(),
        ];

        return $this->render('AppCaseboxCoreBundle::edit.html.twig', $vars);
    }
}
