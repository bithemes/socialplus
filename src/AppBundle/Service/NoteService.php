<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use AppBundle\Entity\Note;

class NoteService {

	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function addNote($user, $type, $message) {
		if(!in_array($type, ['success', 'danger', 'warning', 'info'])) {
			$type = 'info';
		}

		$note = new Note();
		$note->setUser($user);
		$note->setType($type);
		$note->setMessage($message);
		$note->setNotedAt(new \DateTime());

		$this->em->persist($note);
		$this->em->flush();

		return true;
	}

	public function getNotes($user) {
		return $this->em->getRepository('AppBundle:Note')->findBy(['user' => $user]);
	}

	public function removeNote($id, $user) {
		$note = $this->em->getRepository('AppBundle:Note')->findOneBy(['user' => $user, 'id' => $id]);

		if(!$note) {
			return false;
		}

		$this->em->remove($note);
		$this->em->flush();

		return true;
	}
}