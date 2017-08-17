<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\DataCollectorTranslator;

class LocaleService {
	protected $ts;
	protected $translator;

	public function __construct(TokenStorage $ts, DataCollectorTranslator $translator) {
		$this->ts = $ts;
		$this->translator = $translator;
	}

	public function onKernelRequest(FilterControllerEvent $event) {

		if($this->ts->getToken() == null) {
			// Profiler, no token (security turned off)
			return false;
		}
		if($this->ts->getToken()->getUser() && !is_string($this->ts->getToken()->getUser())) {
			#$event->getRequest()->setLocale($this->ts->getToken()->getUser()->getLanguage());
			#$event->getRequest()->getSession()->set('_locale', $this->ts->getToken()->getUser()->getLanguage());
			$this->translator->setLocale($this->ts->getToken()->getUser()->getLanguage());
		} else {
			// Main Controller, not logged in
			return false;
		}
	}
}