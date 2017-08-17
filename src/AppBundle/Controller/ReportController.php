<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Report;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Adapter\ExtLdap\Query;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class ReportController extends Controller {

	/**
	 * @Route("/report-post", name="report_post")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function reportPostAction(Request $request) {
		if($request->isXmlHttpRequest()) {
			// timestamp - adding comment is possible once every 30 seconds
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_report')) {
				if(time() - $session->get('timestamp_report') < $this->getParameter('timestamps')['report']) {
					$timeToWait = $this->getParameter('timestamps')['report'] - (time() - $session->get('timestamp_report'));
					return new JsonResponse('You have to wait '.$timeToWait.' seconds before you can report next post.', 400 );
				} else {
					$session->set('timestamp_report', time());
				}
			} else {
				$session->set('timestamp_report', time());
			}

			$user = $this->getUser();
			// check if post exist
			$targetPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(['id' => $request->get('post_id')]);
			if(!$targetPost || $targetPost->getUser() == $user) { # no post with given id or it is our post
				return new JsonResponse('There is no post with given id.', 400);
			} else {
				// check if report for this post exist
				$report = $this->getDoctrine()->getRepository('AppBundle:Report')->findOneBy(['targetPost' => $targetPost]);
				if(!$report) {
					$report = new Report();
				}

				$reasons = $report->getReasons();

				// check if we didn't already reported this post
				$isReported = false;
				if($reasons) {
					foreach($reasons as $res) {
						if($res['username'] == $user->getUsername()) {
							$isReported = true;
							break;
						}
					}
				}

				if($isReported) {
					return new JsonResponse('You already reported this post.', 400);
				}

				$reasons[] = ['username' => $user->getUsername(), 'reason' => $request->get('reason')];
				$report->setReasons($reasons);
				$report->setTargetPost($targetPost);

				$em = $this->getDoctrine()->getManager();
				$em->persist($report);
				$em->flush();

				return new JsonResponse('Post has been reported. It will be checked soon.', 200);
			}
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}
}
