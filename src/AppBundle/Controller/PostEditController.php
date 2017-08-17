<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;

class PostEditController extends Controller {

	/**
	 * @Route("/post-delete/{id}", name="post_delete")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function postDeleteAction($id) {
		$post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(['user' => $this->getUser(), 'id' => $id]);

		// no post with given criteria
		if(!$post) {
			return $this->redirectToRoute('dashboard');
		}

		// remove post from db
		$em = $this->getDoctrine()->getManager();
		$em->remove($post);
		$em->flush();

		return $this->redirectToRoute('dashboard');
	}

	/**
	 * @Route("/post-edit/{id}", name="post_edit")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function postEditAction(Request $request, $id) {
		$user = $this->getUser();
		$post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(['user' => $user, 'id' => $id]);

		// todo: add notification "The requested post does not exist or you have no privileges to edit it."
		if(!$post) {
			return $this->redirectToRoute('dashboard');
		}
		$postForm = $this->createForm(PostType::class, $post);
		$postForm->add('submit', SubmitType::class, [
			'label' => $this->get('translator')->trans('Edit'),
			'attr' => [
				'class' => 'btn btn-post'
			]
		]);

		$postForm->handleRequest($request);

		if($postForm->isSubmitted() && $postForm->isValid()) {
			$post->setEditedAt(new \DateTime());

			// Save post
			$em = $this->getDoctrine()->getManager();
			$em->persist($post);
			$em->flush();

			return $this->redirectToRoute('dashboard');
		}

		return $this->render('dashboard/post_edit.html.twig', [
			'user' => $user,
			'postForm' => $postForm->createView(),
			'post' => $post,
			'website' => $this->getDoctrine()->getRepository('AppBundle:Website')->findOneBy([])
		]);
	}
}
