<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class SearchController extends Controller {

	/**
	 * @Route("/search-user/{query}", defaults={"query" = "socialplus"}, name="search_user")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function searchUserAction(Request $request, $query) {
		// timestamp
		$session = new Session(new PhpBridgeSessionStorage());
		$session->start();

		if($session->get('timestamp_search')) {
			if(time() - $session->get('timestamp_search') < $this->getParameter('timestamps')['search']) {
				$timeToWait = $this->getParameter('timestamps')['search'] - (time() - $session->get('timestamp_search'));
				$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can perform another search.');
				return $this->redirectToRoute('dashboard');
			} else {
				$session->set('timestamp_search', time());
			}
		} else {
			$session->set('timestamp_search', time());
		}

		$user = $this->getUser();
		$matches = $this->getDoctrine()->getRepository('AppBundle:User')->findByKeyword($query);

		return $this->render('dashboard/search_user.html.twig', [
			'user' => $user,
			'results' => $matches,
			'query' => $query,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}

	/**
	 * @Route("/search/{query}", defaults={"query" = "socialplus"}, name="search")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function searchAction(Request $request, $query) {
		// timestamp
		$session = new Session(new PhpBridgeSessionStorage());
		$session->start();

		if($session->get('timestamp_search')) {
			if(time() - $session->get('timestamp_search') < $this->getParameter('timestamps')['search']) {
				$timeToWait = $this->getParameter('timestamps')['search'] - (time() - $session->get('timestamp_search'));
				$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can perform another search.');
				return $this->redirectToRoute('dashboard');
			} else {
				$session->set('timestamp_search', time());
			}
		} else {
			$session->set('timestamp_search', time());
		}

		$user = $this->getUser();
		$matches = $this->getDoctrine()->getRepository('AppBundle:Post')->findByKeyword($query);

		return $this->render('dashboard/search.html.twig', [
			'user' => $user,
			'results' => $matches,
			'query' => $query,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}
}
