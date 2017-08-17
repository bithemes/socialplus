<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class DashboardController extends Controller {

	/**
	 * @Route("/get_posts", name="get_posts")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function getPostsAction(Request $request) {

		if ($request->isXmlHttpRequest()) {

			$first_id = $request->get('last_id');
			$dashboardPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['user' => $this->getUser()], ['uploadedAt' => 'DESC'], $this->getParameter('website')['posts_per_page'], $first_id);

			if(!$dashboardPosts) {
				return new JsonResponse('There are no posts to return with given criteria.', 400);
			} else {
				return new JsonResponse($this->renderView(':dashboard/parts:dashboard_posts.html.twig', [
					'user' => $this->getUser(),
					'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]),
					'posts' => $dashboardPosts
				]), 200);
			}
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}

	/**
	 * @Route("/dashboard", name="dashboard")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function dashboardAction(Request $request) {
		$user = $this->getUser();

		$post = new Post();
		$postForm = $this->createForm(PostType::class, $post);
		$postForm->add('submit', SubmitType::class, [
			'label' => 'ADD NEW POST...',
			'attr' => [
				'class' => 'btn btn-post'
			]
		]);

		$postForm->handleRequest($request);

		if($postForm->isSubmitted() && $postForm->isValid()) {
			// timestamp - adding post is possible once every 30 seconds
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_new_post')) {
				if(time() - $session->get('timestamp_new_post') < $this->getParameter('timestamps')['new_post']) {
					$timeToWait = $this->getParameter('timestamps')['new_post'] - (time() - $session->get('timestamp_new_post'));
					$this->get('note_service')->addNote($this->getUser(), 'danger', 'You have to wait '.$timeToWait.' seconds before you can add another post.');
					return $this->redirectToRoute('dashboard');
				} else {
					$session->set('timestamp_new_post', time());
				}
			} else {
				$session->set('timestamp_new_post', time());
			}

			$post->setUser($this->getUser());
			$post->setUploadedAt(new \DateTime());

			// Save post
			$em = $this->getDoctrine()->getManager();
			$em->persist($post);
			$em->flush();

			return $this->redirectToRoute('dashboard');
		}

		$dashboardPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['user' => $this->getUser()], ['uploadedAt' => 'DESC'], $this->getParameter('website')['posts_per_page']);

		$qb = $this->getDoctrine()->getManager()->createQueryBuilder();
		$qb->select('count(p.id)');
		$qb->from('AppBundle:Post','p');
		$qb->where('p.user = :user');
		$qb->setParameter('user', $this->getUser());

		$totalPosts = $qb->getQuery()->getSingleScalarResult();

		// sort post comments by likes
		foreach($dashboardPosts as $post) {
			$postComments = $post->getComments();
			$iterator = $postComments->getIterator();
			$iterator->uasort(function ($a, $b) {
				return (count($a->getLikes()) < count($b->getLikes()));
			});
			$post->setComments(new ArrayCollection(iterator_to_array($iterator)));
		}

		return $this->render('dashboard/dashboard.html.twig', [
			'user' => $user,
			'postForm' => $postForm->createView(),
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([]),
			'posts' => $dashboardPosts,
			'total_posts' => $totalPosts
		]);
	}
}
