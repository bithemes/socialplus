<?php

namespace AppBundle\Controller;

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

class UserController extends Controller {

	/**
	 * @Route("/get_user_posts", name="get_user_posts")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function getUserPostsAction(Request $request) {

		if ($request->isXmlHttpRequest()) {

			$first_id = $request->get('last_id');
			$targetUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $request->get('target_user')]);
			$posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['user' => $targetUser], ['uploadedAt' => 'DESC'], $this->getParameter('website')['posts_per_page'], $first_id);

			if(!$posts) {
				return new JsonResponse('There are no posts to return with given criteria.', 400);
			} else {
				return new JsonResponse($this->renderView(':dashboard/parts:user_posts.html.twig', [
					'targetUser' => $targetUser,
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
	 * @Route("/u/{username}", name="user_page")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function userAction($username) {
		$user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

		if(!$user) {
			return $this->redirectToRoute('dashboard');
		}

		$posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['user' => $user], ['uploadedAt' => 'DESC'], $this->getParameter('website')['posts_per_page']);

		// sort post comments by likes
		foreach($posts as $post) {
			$postComments = $post->getComments();
			$iterator = $postComments->getIterator();
			$iterator->uasort(function ($a, $b) {
				return (count($a->getLikes()) < count($b->getLikes()));
			});
			$post->setComments(new ArrayCollection(iterator_to_array($iterator)));
		}

		/** @var QueryBuilder $qb */
		$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
		$qb->select('count(p.id)');
		$qb->from('AppBundle:Post','p');
		$qb->where('p.user = :user');
		$qb->setParameter('user', $user);

		$totalPosts = $qb->getQuery()->getSingleScalarResult();

		return $this->render('dashboard/user.html.twig', [
			'targetUser' => $user,
			'user' => $this->getUser(),
			'total_posts' => $totalPosts,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]),
			'posts' => $posts
		]);
	}
}
