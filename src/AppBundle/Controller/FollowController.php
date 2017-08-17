<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Follow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class FollowController extends Controller {

	/**
	 * @Route("/unfollow/{username}", name="unfollow")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function unfollowAction(Request $request, $username) {
		// timestamp
		$session = new Session(new PhpBridgeSessionStorage());
		$session->start();
		if($session->get('timestamp_follow')) {
			if(time() - $session->get('timestamp_follow') < $this->getParameter('timestamps')['follow']) {
				$timeToWait = $this->getParameter('timestamps')['follow'] - (time() - $session->get('timestamp_follow'));
				$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can follow/unfollow next user.');
				return $this->redirectToRoute('dashboard');
			} else {
				$session->set('timestamp_follow', time());
			}
		} else {
			$session->set('timestamp_follow', time());
		}

		$user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

		// no user with given criteria or self-follow attempt
		if(!$user || $user == $this->getUser()) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if not already followed
		$followed = $this->getDoctrine()->getRepository('AppBundle:Follow')->findOneBy(['targetUser' => $user, 'user' => $this->getUser()]);

		// target is already followed by this user
		if(!$followed) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// update in db
		$em = $this->getDoctrine()->getManager();
		$em->remove($followed);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}

	/**
	 * @Route("/follow/{username}", name="follow")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function followAction(Request $request, $username) {
		// timestamp
		$session = new Session(new PhpBridgeSessionStorage());
		$session->start();
		if($session->get('timestamp_follow')) {
			if(time() - $session->get('timestamp_follow') < $this->getParameter('timestamps')['follow']) {
				$timeToWait = $this->getParameter('timestamps')['follow'] - (time() - $session->get('timestamp_follow'));
				$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can follow/unfollow next user.');
				return $this->redirectToRoute('dashboard');
			} else {
				$session->set('timestamp_follow', time());
			}
		} else {
			$session->set('timestamp_follow', time());
		}

		// check if user exist or we are not trying to follow ourselfs
		$user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

		// no user with given criteria
		if(!$user || $user == $this->getUser()) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if not already followed
		$followed = $this->getDoctrine()->getRepository('AppBundle:Follow')->findOneBy(['targetUser' => $user, 'user' => $this->getUser()]);

		// target is already followed by this user
		if($followed) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// update in db
		$follow = new Follow();
		$follow->setUser($this->getUser());
		$follow->setTargetUser($user);
		$follow->setFollowedAt(new \DateTime());

		$em = $this->getDoctrine()->getManager();
		$em->persist($follow);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}
}
