<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="post_like")
 */
class PostLike {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="likes")
	 */
	private $post;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $likedAt;

	public function __construct() {

	}

	public function getId() {
		return $this->id;
	}

	public function setId( $id ) {
		$this->id = $id;
	}

	public function getUser() {
		return $this->user;
	}

	public function setUser( $user ) {
		$this->user = $user;
	}

	public function getPost() {
		return $this->post;
	}

	public function setPost( $post ) {
		$this->post = $post;
	}

	public function getLikedAt() {
		return $this->likedAt;
	}

	public function setLikedAt( $likedAt ) {
		$this->likedAt = $likedAt;
	}
}
