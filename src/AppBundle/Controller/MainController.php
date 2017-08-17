<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MainController extends Controller {

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request) {

        $authUtils = $this->get('security.authentication_utils');
        $lastErrorLogin = null;
        $lastUsername = $authUtils->getLastUsername();

        // Register
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('submit', SubmitType::class, [
            'label' => 'Create',
            'attr' => [
                'class' => 'btn btn-info'
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Encode password
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Save user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            # autologin and redirect to dashboard
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles() );
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('dashboard');
        }

        $lastErrorLogin = $authUtils->getLastAuthenticationError();

        return $this->render('main/index.html.twig', [
            'login' => [
                'last_username' => $lastUsername,
                'error' => $lastErrorLogin
            ],
            'register' => [
                'form' => $form->createView()
            ],
            'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
        ]);
    }
}
