<?php

namespace App\DashboardBundle\Controller;

use App\CaseboxCoreBundle\Entity\Core;
use App\DashboardBundle\Service\MessageService;
use App\DashboardBundle\Traits\DateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                    'actions' => $this->get('app_casebox_core.service.casebox_core_service')->getActionsHtml($core),
                ];
            }
        }
        
        return $this->render('AppDashboardBundle:dashboard:index.html.twig', $vars);
    }

    /**
     * @Route("/admin/install", name="admin_install")
     * @return Response
     */
    public function installAction(Request $request)
    {
        $vars['items'] = [];
        $cores = $this->get('app_casebox_core.service.casebox_core_service')->getAllCores();

        if (empty($cores)) {
            $form = $this->setupGetForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                if (empty($data['passphrase'])) {
                    $this->addFlash('warning', MessageService::PASSPHRASE_NOT_FOUND);

                    return $this->redirectToRoute('admin_install');
                }

                $result = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->passphrase($data['passphrase']);
                
                if (empty($result)) {
                    $this->addFlash('warning', MessageService::CRYPTFS_UNABLE_TO_MOUNT);
                    
                    return $this->redirectToRoute('admin_install');
                } else {
                    return $this->redirectToRoute('admin');
                }
            }

            $vars['form'] = $form->createView();

            return $this->render('AppDashboardBundle:dashboard:startup.html.twig', $vars);
        }

        return $this->render('AppDashboardBundle:dashboard:setup.html.twig', $vars);
    }

    /**
     * @Route("/admin/ecryptfs", name="admin_ecryptfs")
     * @return Response
     */
    public function ecryptfsAction(Request $request)
    {
        $vars['items'] = [];

        $form = $this->setupGetForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (empty($data['passphrase'])) {
                $this->addFlash('warning', MessageService::PASSPHRASE_NOT_FOUND);

                return $this->redirectToRoute('admin_install');
            }

            $result = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->passphrase($data['passphrase']);
            
            if (empty($result)) {
                $this->addFlash('warning', MessageService::CRYPTFS_UNABLE_TO_MOUNT);
                
                return $this->redirectToRoute('admin_install');
            } else {
                return $this->redirectToRoute('admin');
            }
        }

        $vars['form'] = $form->createView();

        return $this->render('AppDashboardBundle:dashboard:setup.html.twig', $vars);
    }


    /**
     * @Route("/admin/rsa", name="admin_rsa")
     * @return Response
     */
    public function rsaKeyAction()
    {
        return $this->render('AppDashboardBundle:rsa:index.html.twig');
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function setupGetForm()
    {
        $builder = $this->createFormBuilder([]);
        $builder->add('passphrase', PasswordType::class, [
                'label' => 'Passphrase',
                'required' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );

        $builder->add('delete', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary form-control',
                ],
            ]
        );

        return $builder->getForm();
    }
}
