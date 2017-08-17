<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Entity\Website;

class WebsiteService {
	protected $em;
	protected $params;

	public function __construct(EntityManager $em, $params) {
		$this->em = $em;
		$this->params = $params;
	}

	// triggered before going inside controller
	public function onKernelRequest(FilterControllerEvent $event) {
		if(!$this->em->isOpen()) {
			$this->em = $this->em->create($this->em->getConnection(), $this->em->getConfiguration());
		}

		if($this->em->getRepository('AppBundle:Website')->findOneBy([])) {
			return true;
		} else {
			# There is no single row in Website Table so add default settings from parameters.yml
			$settings = new Website();
			$settings->setTitle($this->params['title']);
			$settings->setSubtitle($this->params['subtitle']);
			$settings->setPostsPerPage($this->params['posts_per_page']);
			$settings->setMainColor($this->params['main_color']);

			$this->em->persist($settings);
			$this->em->flush();

			return true;
		}
	}
}