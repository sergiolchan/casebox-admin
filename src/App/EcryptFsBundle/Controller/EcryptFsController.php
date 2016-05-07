<?php

namespace App\EcryptFsBundle\Controller;

use App\DashboardBundle\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class EcryptFsController
 */
class EcryptFsController extends Controller
{
    /**
     * @Route("/admin/security", name="admin_security")
     * @param Request $request
     *
     * @return Response
     */
    public function installAction(Request $request)
    {
        $cores = $this->get('app_casebox_core.repository.core_repository')->find();

        $form = $this->setupGetForm($cores);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (empty($data['passphrase'])) {
                $this->addFlash('warning', MessageService::PASSPHRASE_NOT_FOUND);

                return $this->redirectToRoute('admin_security');
            }
            
            if (empty($cores) && ($data['passphrase'] != $data['passphrase_confirmation'])) {
                $this->addFlash('warning', MessageService::PASSPHRASE_NOT_MATCH);

                return $this->redirectToRoute('admin_security');
            }

            $result = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->passphrase($data['passphrase']);

            if (empty($result)) {
                $this->addFlash('warning', MessageService::CRYPTFS_UNABLE_TO_MOUNT);

                return $this->redirectToRoute('admin_security');
            } else {
                $this->container->get('app_dashboard.service.redis_service')->set('ecryptfs_ready', 1);
                    
                return $this->redirectToRoute('admin_security_setup');
            }
        }

        $vars['form'] = $form->createView();
        $vars['cores'] = $cores;

        return $this->render('AppEcryptFsBundle::passphrase.html.twig', $vars);
    }

    /**
     * @Route("/admin/security/setup", name="admin_security_setup")
     * @param Request $request
     *
     * @return Response
     */
    public function setupAction(Request $request)
    {
        $isEncrypted = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->isEncrypted();

        if (!empty($isEncrypted)) {
            return $this->redirectToRoute('admin');
        }

        $vars = [
            'title' => 'Security setup',
            'message' => MessageService::CRYPTFS_PLEASE_WAIT,
        ];
        
        return $this->render('AppEcryptFsBundle::empty.html.twig', $vars);
    }


    /**
     * @Route("/admin/security/reload", name="admin_security_reload")
     * @param Request $request
     *
     * @return Response
     */
    public function reloadAction(Request $request)
    {
        $isReloadNeede = $this->container->get('app_ecrypt_fs.service.ecrypt_fs_service')->isReloadNeeded();

        if (!$isReloadNeede) {
            return $this->redirectToRoute('admin');
        }

        $vars = [
            'title' => 'Vagrant reload needed!',
            'message' => MessageService::PASSPHRASE_REALOD,
        ];

        return $this->render('AppEcryptFsBundle::empty.html.twig', $vars);
    }


    /**
     * @param array|null $confirm
     * @return \Symfony\Component\Form\Form
     */
    protected function setupGetForm($confirm = null)
    {
        $builder = $this->createFormBuilder([]);
        $builder->add(
            'passphrase',
            PasswordType::class,
            [
                'label' => 'Passphrase',
                'required' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );

        if (empty($confirm)) {
            $builder->add(
                'passphrase_confirmation',
                PasswordType::class,
                [
                    'label' => 'Passphrase confirmation',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-group form-control',
                    ],
                ]
            );
        }

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary form-control',
                ],
            ]
        );

        return $builder->getForm();
    }
}
