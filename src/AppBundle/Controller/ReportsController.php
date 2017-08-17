<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Report;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Query;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;

class ReportsController extends Controller {

	/**
	 * @Route("/accept-report/{report}", name="accept_report")
	 * @Security("has_role('ROLE_MODERATOR')")
	 */
	public function acceptReportAction($report) {
		$report = $this->getDoctrine()->getRepository('AppBundle:Report')->findOneBy(['id' => $report]);
		if(!$report) {
			return $this->redirectToRoute('reports');
		}

		// remove post and report
		$em = $this->getDoctrine()->getManager();
		$em->remove($report->getTargetPost());
		$em->remove($report);
		$em->flush();

		return $this->redirectToRoute('reports');
	}

	/**
	 * @Route("/reject-report/{report}", name="reject_report")
	 * @Security("has_role('ROLE_MODERATOR')")
	 */
	public function rejectReportAction($report) {
		$report = $this->getDoctrine()->getRepository('AppBundle:Report')->findOneBy(['id' => $report]);
		if(!$report) {
			return $this->redirectToRoute('reports');
		}

		// remove report but not post
		$em = $this->getDoctrine()->getManager();
		$em->remove($report);
		$em->flush();

		return $this->redirectToRoute('reports');
	}

	/**
	 * @Route("/reports", name="reports")
	 * @Security("has_role('ROLE_MODERATOR')")
	 */
	public function reportsPostAction() {
		$user = $this->getUser();
		$reports = $this->getDoctrine()->getRepository('AppBundle:Report')->findAll();

		uasort($reports, function ($a, $b) {
			return (count($a->getReasons()) < count($b->getReasons()));
		});

		return $this->render('dashboard/reports.html.twig', [
			'user' => $user,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]),
			'reports' => $reports
		]);
	}
}
