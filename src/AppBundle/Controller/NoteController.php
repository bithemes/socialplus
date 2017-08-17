<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends Controller {

	/**
	 * @Route("/note-remove", name="note_remove")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function removeNoteAction(Request $request) {
		if ($request->isXmlHttpRequest()) {
			if($this->get('note_service')->removeNote($request->get('id'), $this->getUser())) {
				return new JsonResponse('Note deleted.', 200);
			} else {
				return new JsonResponse('A note with given id does not exist or does not belong to logged user.', 400);
			}
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}
}