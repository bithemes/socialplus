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
use AppBundle\Form\BanType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use AppBundle\Entity\Ban;

class BanController extends Controller {

	/**
	 * @Route("/unban/{username}", name="unban")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function unbanAction(Request $request, $username) {
		$user = $this->getUser();
		if($user->getUsername() == $username) {
			return $this->redirectToRoute('bans');
		}

		$targetUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $username]);
		if(!$targetUser) {
			$this->get('note_service')->addNote($user, 'danger', 'No user found with given username.');
			return $this->redirectToRoute('bans');
		}

		// check if banned
		$ban = $this->getDoctrine()->getRepository('AppBundle:Ban')->findOneBy(['user' => $user, 'targetUser' => $targetUser]);
		if(!$ban) {
			return $this->redirectToRoute('bans');
		}

		$em = $this->getDoctrine()->getManager();
		$em->remove($ban);
		$em->flush();

		return $this->redirectToRoute('bans');
	}

	/**
	 * @Route("/bans", name="bans")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function bansAction(Request $request) {
		$user = $this->getUser();
		$form = $this->createForm(BanType::class);

		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			// check if user exist
			$targetUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $form->get('targetUser')->getData()]);
			if(!$targetUser) {
				$this->get('note_service')->addNote($user, 'danger', 'No user found with given username.');
				return $this->redirectToRoute('bans');
			}

			if($targetUser == $user) {
				return $this->redirectToRoute('bans');
			}

			// check if not already banned
			$isBanned = $this->getDoctrine()->getRepository('AppBundle:Ban')->findOneBy(['user' => $user, 'targetUser' => $targetUser]);
			if($isBanned) {
				return $this->redirectToRoute('bans');
			}

			// add to bans
			$ban = new Ban();
			$ban->setUser($user);
			$ban->setTargetUser($targetUser);
			$ban->setBannedAt(new \DateTime());

			$em = $this->getDoctrine()->getManager();
			$em->persist($ban);
			$em->flush();

			return $this->redirectToRoute('bans');

		}

		return $this->render('dashboard/bans.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}

}
