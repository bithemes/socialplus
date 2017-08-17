<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\WebsiteType;

class SystemController extends Controller {

	/**
	 * @Route("/website-settings", name="website_settings")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function websiteSettingsAction(Request $request) {
		$user = $this->getUser();

		$webSettings = $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]);

		$form = $this->createForm(WebsiteType::class, $webSettings);

		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			// Save user
			$em = $this->getDoctrine()->getManager();
			$em->persist($webSettings);
			$em->flush();
			$this->get('note_service')->addNote($this->getUser(), 'success', $this->get('translator')->trans('Website settings changes were saved!'));
		}

		return $this->render('dashboard/website_settings.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}
}