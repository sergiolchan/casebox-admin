<?php

namespace App\SecurityBundle\Controller;

use App\DashboardBundle\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityController
 */
class SecurityController extends Controller
{
    /**
     * @Route("/admin/security/app", name="admin_security_app")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $vars['has_password'] = $this->get('app_security.service.security_service')->getMasterPassword();
        $form = $this->securityGetForm($vars['has_password']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (empty($data['password'])) {
                $this->addFlash('warning', MessageService::PASSWORD_NOT_FOUND);

                return $this->redirectToRoute('admin_security_app');
            }

            if (empty($vars['has_password']) && ($data['password'] != $data['password_confirmation'])) {
                $this->addFlash('warning', MessageService::PASSWORD_NOT_MATCH);

                return $this->redirectToRoute('admin_security_app');
            }

            $result = $this->container->get('app_security.service.security_service')->setMasterPassword(
                $data['password']
            );

            if (empty($result)) {
                $this->addFlash('warning', MessageService::PASSWORD_INVALID);

                return $this->redirectToRoute('admin_security_app');
            } else {
                return $this->redirectToRoute('admin');
            }
        }

        $vars['form'] = $form->createView();

        return $this->render('AppSecurityBundle::index.html.twig', $vars);
    }

    /**
     * @Route("/admin/security/logout", name="admin_security_logout")
     * @param Request $request
     *
     * @return Response
     */
    public function logoutAction(Request $request)
    {
        $this->get('session')->remove('_user');

        return $this->redirectToRoute('admin');
    }

    /**
     * @param array|null $confirm
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function securityGetForm($confirm = null)
    {
        $builder = $this->createFormBuilder([]);
        $builder->add(
            'password',
            PasswordType::class,
            [
                'label' => 'Password',
                'required' => true,
                'attr' => [
                    'class' => 'form-group form-control',
                ],
            ]
        );

        if (empty($confirm)) {
            $builder->add(
                'password_confirmation',
                PasswordType::class,
                [
                    'label' => 'Password confirmation',
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
