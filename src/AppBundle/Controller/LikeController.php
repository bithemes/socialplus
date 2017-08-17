<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CommentLike;
use AppBundle\Entity\PostLike;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;

class LikeController extends Controller {

	/**
	 * @Route("/comment-dislike/{id}", name="comment_dislike")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function commentDislikeAction(Request $request, $id) {
		// check if post exist
		$comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneBy(['id' => $id]);

		// no post with given criteria
		if(!$comment) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if already liked
		$liked = $this->getDoctrine()->getRepository('AppBundle:CommentLike')->findOneBy(['user' => $this->getUser(), 'comment' => $comment]);

		// post is NOT already liked by this user
		if(!$liked) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// remove from db
		$em = $this->getDoctrine()->getManager();
		$em->remove($liked);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}

	/**
	 * @Route("/comment-like/{id}", name="comment_like")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function commentLikeAction(Request $request, $id) {
		// check if comment exist
		$comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneBy(['id' => $id]);

		// no post with given criteria
		if(!$comment) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if banned
		$ban = $this->getDoctrine()->getRepository('AppBundle:Ban')->findOneBy(['user' => $comment->getUser(), 'targetUser' => $this->getUser()]);
		if($ban) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if not already liked
		$liked = $this->getDoctrine()->getRepository('AppBundle:CommentLike')->findOneBy(['user' => $this->getUser(), 'comment' => $comment]);

		// post is already liked by this user
		if($liked) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// update in db
		$like = new CommentLike();
		$like->setComment($comment);
		$like->setUser($this->getUser());
		$like->setLikedAt(new \DateTime());

		$em = $this->getDoctrine()->getManager();
		$em->persist($like);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}

	/**
	 * @Route("/post-dislike/{id}", name="post_dislike")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function postDislikeAction(Request $request, $id) {
		// check if post exist
		$post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(['id' => $id]);

		// no post with given criteria
		if(!$post) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if already liked
		$liked = $this->getDoctrine()->getRepository('AppBundle:PostLike')->findOneBy(['user' => $this->getUser(), 'post' => $post]);

		// post is NOT already liked by this user
		if(!$liked) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// remove from db
		$em = $this->getDoctrine()->getManager();
		$em->remove($liked);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}

	/**
	 * @Route("/post-like/{id}", name="post_like")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function postLikeAction(Request $request, $id) {
		// check if post exist
		$post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(['id' => $id]);

		// no post with given criteria
		if(!$post) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if banned
		$ban = $this->getDoctrine()->getRepository('AppBundle:Ban')->findOneBy(['user' => $post->getUser(), 'targetUser' => $this->getUser()]);
		if($ban) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// check if not already liked
		$liked = $this->getDoctrine()->getRepository('AppBundle:PostLike')->findOneBy(['user' => $this->getUser(), 'post' => $post]);

		// post is already liked by this user
		if($liked) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		// update in db
		$like = new PostLike();
		$like->setPost($post);
		$like->setUser($this->getUser());
		$like->setLikedAt(new \DateTime());

		$em = $this->getDoctrine()->getManager();
		$em->persist($like);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}
}
