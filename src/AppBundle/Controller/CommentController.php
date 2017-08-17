<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\PostLike;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class CommentController extends Controller {
	/**
	 * @Route("/reply-comment", name="reply_comment")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function replyCommentAction(Request $request) {
		if ($request->isXmlHttpRequest()) {
			// timestamp - adding comment is possible once every 30 seconds
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_new_comment')) {
				if(time() - $session->get('timestamp_new_comment') < $this->getParameter('timestamps')['new_comment']) {
					$timeToWait = $this->getParameter('timestamps')['new_comment'] - (time() - $session->get('timestamp_new_comment'));
					return new JsonResponse('You have to wait '.$timeToWait.' seconds before you can add another comment.', 400 );
				} else {
					$session->set('timestamp_new_comment', time());
				}
			} else {
				$session->set('timestamp_new_comment', time());
			}

			// check if post exist
			$post = $this->getDoctrine()->getRepository( 'AppBundle:Post' )->findOneBy([ 'id' => $request->get('post_id') ]);
			if(!$post) {
				return new JsonResponse($this->get('translator')->trans('Post with given id does not exist.'), 400);
			}

			// check if comment exist
			$parentComment = $this->getDoctrine()->getRepository( 'AppBundle:Comment' )->findOneBy(['id' => $request->get('comment_id'), 'post' => $post, 'parentComment' => NULL ]);

			// no comment with given comment_id and post and no parent comment
			if(!$parentComment) {
				return new JsonResponse($this->get('translator')->trans('Comment with given criteria does not exist or it is a reply.'), 400);
			}

			// check if we are not banned
			$ban = $this->getDoctrine()->getRepository('AppBundle:Ban')->findOneBy(['targetUser' => $this->getUser(), 'user' => $parentComment->getUser()]);
			if($ban) {
				return new JsonResponse('The parent comment owner has banned you.', 400);
			}

			$comment = new Comment();
			$comment->setPost($post);
			$comment->setUser($this->getUser());
			$comment->setParentComment($parentComment);
			$comment->setContent($request->get('content'));
			$comment->setCommentedAt(new \DateTime());

			$validator = $this->get( 'validator' );
			$errors    = $validator->validate( $comment );

			if(count($errors) > 0) {
				$errorMessages = '';
				for ( $i = 0; $i < count( $errors ); $i++ ) {
					$errorMessages .= $errors->get( $i )->getMessage();
				}
				return new JsonResponse( $errorMessages, 400 );
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist( $comment );
			$em->flush();

			return new JsonResponse( $this->get('translator')->trans('Comment added.'), 200 );
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}

	/**
	 * @Route("/comment-delete/{id}", name="comment_delete")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function commentDeleteAction(Request $request, $id) {
		$comment = $this->getDoctrine()->getRepository('AppBundle:Comment')->findOneBy(['id' => $id]);

		// check if comment does not exist OR we are neither comment's author nor post author
		if(!$comment || ($comment->getUser() != $this->getUser() && $comment->getPost()->getUser() != $this->getUser()) ) {
			if($request->headers->get('referer') != NULL) {
				return $this->redirect($request->headers->get('referer'));
			} else {
				return $this->redirectToRoute('dashboard');
			}
		}

		$em = $this->getDoctrine()->getManager();
		$em->remove($comment);
		$em->flush();

		if($request->headers->get('referer') != NULL) {
			return $this->redirect($request->headers->get('referer'));
		} else {
			return $this->redirectToRoute('dashboard');
		}
	}

	/**
	 * @Route("/post-comment", name="post_comment")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function addCommentAction(Request $request) {
		if ($request->isXmlHttpRequest()) {
			// timestamp - adding comment is possible once every 30 seconds
			$session = new Session(new PhpBridgeSessionStorage());
			$session->start();

			if($session->get('timestamp_new_comment')) {
				if(time() - $session->get('timestamp_new_comment') < $this->getParameter('timestamps')['new_comment']) {
					$timeToWait = $this->getParameter('timestamps')['new_comment'] - (time() - $session->get('timestamp_new_comment'));
					return new JsonResponse('You have to wait '.$timeToWait.' seconds before you can add another comment.', 400 );
				} else {
					$session->set('timestamp_new_comment', time());
				}
			} else {
				$session->set('timestamp_new_comment', time());
			}

			// check if post exist
			$post = $this->getDoctrine()->getRepository( 'AppBundle:Post' )->findOneBy( [ 'id' => $request->get( 'post_id' ) ] );

			// no post with given post_id
			if (!$post) {
				return $this->redirectToRoute( 'dashboard' );
			}

			// check if we are not banned
			$ban = $this->getDoctrine()->getRepository('AppBundle:Ban')->findOneBy(['targetUser' => $this->getUser(), 'user' => $post->getUser()]);
			if($ban) {
				return new JsonResponse('The post owner has banned you.', 400);
			}

			$comment = new Comment();
			$comment->setPost( $post );
			$comment->setUser( $this->getUser() );
			$comment->setContent( $request->get( 'content' ) );
			$comment->setCommentedAt( new \DateTime() );

			$validator = $this->get( 'validator' );
			$errors    = $validator->validate( $comment );

			if ( count( $errors ) > 0 ) {
				$errorMessages = '';
				for ( $i = 0; $i < count( $errors ); $i ++ ) {
					$errorMessages .= $errors->get( $i )->getMessage();
				}

				return new JsonResponse( $errorMessages, 400 );
			}

			$em = $this->getDoctrine()->getManager();
			$em->persist( $comment );
			$em->flush();

			return new JsonResponse( $this->get('translator')->trans('Comment added.'), 200 );
		}

		// Not Ajax request
		return new JsonResponse('Bad Request Type', 403);
	}
}