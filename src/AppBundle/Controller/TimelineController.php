<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Follow;
use Doctrine\DBAL\Query\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class TimelineController extends Controller {

	/**
	 * @Route("/get_timeline_posts", name="get_timeline_posts")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function getTimelinePostsAction(Request $request) {

		if ($request->isXmlHttpRequest()) {

			$first_id = $request->get('last_id');
			$user = $this->getUser();

			$users = [];
			foreach($user->getFollows() as $follow) {
				$users[] = $follow->getTargetUser();
			}

			$posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['user' => $users], ['uploadedAt' => 'DESC'], $this->getParameter('website')['posts_per_page'], $first_id);

			if(!$posts) {
				return new JsonResponse('There are no posts to return with given criteria.', 400);
			} else {
				return new JsonResponse($this->renderView(':dashboard/parts:timeline_posts.html.twig', [
					'user' => $this->getUser(),
					'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]),
					'posts' => $posts
				]), 200);
			}
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}

	/**
	 * @Route("/timeline", name="timeline")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function timelineAction(Request $request) {
		$user = $this->getUser();

		$users = [];
		$ids = [];
		foreach($user->getFollows() as $follow) {
			/** @var Follow $follow */
			$usr = $follow->getTargetUser();
			$users[] = $usr;
			$ids[] = $usr->getId();
		}

		$posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['user' => $users], ['uploadedAt' => 'DESC'], $this->getParameter('website')['posts_per_page']);

		/** @var QueryBuilder $qb */
		$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
		$qb->select('count(p.id)');
		$qb->from('AppBundle:Post','p');
		$qb->where($qb->expr()->in('p.user', $ids));
		$totalPosts = $qb->getQuery()->getSingleScalarResult();

		return $this->render('dashboard/timeline.html.twig', [
			'user' => $user,
			'posts' => $posts,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]),
			'total_posts' => $totalPosts
		]);
	}
}
