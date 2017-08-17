<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Follow {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="follows")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="followers")
	 */
	private $targetUser;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $followedAt;

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

	public function getTargetUser() {
		return $this->targetUser;
	}

	public function setTargetUser( $targetUser ) {
		$this->targetUser = $targetUser;
	}

	public function getFollowedAt() {
		return $this->followedAt;
	}

	public function setFollowedAt( $followedAt ) {
		$this->followedAt = $followedAt;
	}

}
