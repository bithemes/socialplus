<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\PersonalType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class SettingsController extends Controller {

	/**
	 * @Route("/update_description", name="update_description")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function updateDescriptionAction(Request $request) {
		if ($request->isXmlHttpRequest()) {
			// timestamp
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_settings')) {
				if(time() - $session->get('timestamp_settings') < $this->getParameter('timestamps')['settings']) {
					$timeToWait = $this->getParameter('timestamps')['settings'] - (time() - $session->get('timestamp_settings'));
					return new JsonResponse('You have to wait '.$timeToWait.' seconds before you can update your settings.', 400 );
				} else {
					$session->set('timestamp_settings', time());
				}
			} else {
				$session->set('timestamp_settings', time());
			}

			$user = $this->getUser();
			$user->setDescription($request->get('description'));

			$validator = $this->get('validator');
			$errors = $validator->validate($user);

			if(count($errors) > 0) {
				$errorMessages = '';
				for($i=0; $i<count($errors); $i++) {
					$errorMessages .= $errors->get($i)->getMessage();
				}
				return new JsonResponse($errorMessages, 400);
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			return new JsonResponse($this->get('translator')->trans('Description has been changed successfully.'), 200);
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}

	/**
	 * @Route("/personal-settings", name="personal_settings")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function personalSettingsAction(Request $request) {
		$user = $this->getUser();
		$form = $this->createForm(PersonalType::class, $user);
		$form->add('submit', SubmitType::class, [
			'label' => 'Update',
			'attr' => [
				'class' => 'btn btn-info'
			]
		]);

		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			// timestamp
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_settings')) {
				if(time() - $session->get('timestamp_settings') < $this->getParameter('timestamps')['settings']) {
					$timeToWait = $this->getParameter('timestamps')['settings'] - (time() - $session->get('timestamp_settings'));
					$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can update you settings.');
					return $this->redirectToRoute('personal_settings');
				} else {
					$session->set('timestamp_settings', time());
				}
			} else {
				$session->set('timestamp_settings', time());
			}

			// Save user
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);

			if($form->get('avatarFile')->getData() != NULL ) {
				$post = new Post();
				$post->setUser($this->getUser());
				$post->setContent($user->getName().' '.$user->getSurname().' has changed his avatar image.');
				$post->setUploadedAt(new \DateTime());
				$em->persist($post);
			}

			if($form->get('backgroundFile')->getData() != NULL ) {
				$postBg = new Post();
				$postBg->setUser($this->getUser());
				$postBg->setContent($user->getName().' '.$user->getSurname().' has changed his background image.');
				$postBg->setUploadedAt(new \DateTime());
				$em->persist($postBg);
			}
			$em->flush();
			$this->get('note_service')->addNote($this->getUser(), 'success', $this->get('translator')->trans('Your changes were saved!'));
		}

		return $this->render('dashboard/personal_settings.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}

	/**
	 * @Route("/settings", name="settings")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function settingsAction(Request $request) {
		$user = $this->getUser();
		$form = $this->createForm(UserType::class, $user, [
			'validation_groups' => ['Default'],
			'required' => false
		]);
		$form->add('submit', SubmitType::class, [
			'label' => 'Update',
			'attr' => [
				'class' => 'btn btn-info'
			]
		]);

		// should be rest
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			// timestamp
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_settings')) {
				if(time() - $session->get('timestamp_settings') < $this->getParameter('timestamps')['settings']) {
					$timeToWait = $this->getParameter('timestamps')['settings'] - (time() - $session->get('timestamp_settings'));
					$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can update you settings.');
					return $this->redirectToRoute('settings');
				} else {
					$session->set('timestamp_settings', time());
				}
			} else {
				$session->set('timestamp_settings', time());
			}

			// Encode password
			if($user->getPlainPassword()) {
				$password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);
			}

			// Save user
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			$this->get('note_service')->addNote($this->getUser(), 'success', $this->get('translator')->trans('Your changes were saved!'));
		}

		return $this->render('dashboard/settings.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}
}
