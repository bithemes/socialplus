<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;
use AppBundle\Form\MessageType;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class MessagesController extends Controller {

	/**
	 * @Route("/message-remove/{id}", name="message_remove")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function removeMessageAction(Request $request, $id) {
		$msg = $this->getDoctrine()->getRepository( 'AppBundle:Message' )->findOneBy(['id' => $id, 'toUser' => $this->getUser()]);
		if(!$msg) {
			return $this->redirectToRoute('dashboard');
		}

		$em = $this->getDoctrine()->getManager();
		$em->remove($msg);
		$em->flush();

		return $this->redirectToRoute('messages');
	}

	/**
	 * @Route("/message-new", name="message_new")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function newMessageAction(Request $request) {
		// timestamp
		$session = new Session(new PhpBridgeSessionStorage());
		$session->start();

		if($session->get('timestamp_message')) {
			if(time() - $session->get('timestamp_message') < $this->getParameter('timestamps')['message']) {
				$timeToWait = $this->getParameter('timestamps')['message'] - (time() - $session->get('timestamp_message'));
				$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can send new message.');
				return $this->redirectToRoute('dashboard');
			} else {
				$session->set('timestamp_message', time());
			}
		} else {
			$session->set('timestamp_message', time());
		}

		$user = $this->getUser();
		$message = new Message();
		$messageForm = $this->createForm(MessageType::class, $message);

		$messageForm->handleRequest($request);

		if($messageForm->isSubmitted() && $messageForm->isValid()) {
			$toUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $messageForm->get('username')->getData()]);
			if(!$toUser) {
				$messageForm->get('username')->addError(new FormError('User with given username does not exist.'));
				return $this->render('dashboard/message_new.html.twig', [
					'user' => $user,
					'messageForm' => $messageForm->createView()
				]);
			}

			$message->setToUser(($toUser));
			$message->setFromUser($this->getUser());
			$message->setSendAt(new \DateTime());

			// Save post
			$em = $this->getDoctrine()->getManager();
			$em->persist($message);
			$em->flush();

			$this->get('note_service')->addNote($this->getUser(), 'success', 'Message sent to @'.$toUser->getUsername());

			return $this->redirectToRoute('messages');
		}

		// form (add user from url if is set)

		return $this->render('dashboard/message_new.html.twig', [
			'user' => $user,
			'messageForm' => $messageForm->createView(),
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}

	/**
	 * @Route("/message-checked", name="message_check")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function checkMessageAction(Request $request) {
		if($request->isXmlHttpRequest()) {
			$msg = $this->getDoctrine()->getRepository( 'AppBundle:Message' )->findOneBy(['id' => $request->get('message_id'), 'toUser' => $this->getUser()]);

			if(!$msg) {
				return $this->redirectToRoute('dashboard');
			}

			$msg->setChecked(true);
			$em = $this->getDoctrine()->getManager();
			$em->persist($msg);
			$em->flush();

			return new JsonResponse('Marked as read.', 200);
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}

	/**
	 * @Route("/messages/{page}", defaults={"page" = 1}, name="messages")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function messagesAction(Request $request, $page) {
		$user = $this->getUser();
		$messages = $this->getDoctrine()->getRepository('AppBundle:Message')->findBy(['toUser' => $user], ['sendAt' => 'DESC']);
		$pagination = $this->get('knp_paginator')->paginate($messages, $page, 10);
		$notes = $this->get('note_service')->getNotes($user);

		return $this->render('dashboard/messages.html.twig', [
			'user' => $user,
			'messages' => $messages,
			'pagination' => $pagination,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}
}
