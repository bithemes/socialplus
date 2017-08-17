<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 */
class Report {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="array")
	 */
	private $reasons;

	/**
	 * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 */
	private $reportedAt;

	/**
	 * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
	 */
	private $checked; // nullable

	/**
	 * @ORM\OneToOne(targetEntity="AppBundle\Entity\Post")
	 */
	private $targetPost;

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getUsers() {
		return $this->users;
	}

	/**
	 * @param mixed $users
	 */
	public function setUsers( $users ) {
		$this->users = $users;
	}

	/**
	 * @return mixed
	 */
	public function getReasons() {
		return $this->reasons;
	}

	/**
	 * @param mixed $reasons
	 */
	public function setReasons( $reasons ) {
		$this->reasons = $reasons;
	}

	/**
	 * @return mixed
	 */
	public function getReportedAt() {
		return $this->reportedAt;
	}

	/**
	 * @param mixed $reportedAt
	 */
	public function setReportedAt( $reportedAt ) {
		$this->reportedAt = $reportedAt;
	}

	/**
	 * @return mixed
	 */
	public function getChecked() {
		return $this->checked;
	}

	/**
	 * @param mixed $checked
	 */
	public function setChecked( $checked ) {
		$this->checked = $checked;
	}

	/**
	 * @return mixed
	 */
	public function getTargetPost() {
		return $this->targetPost;
	}

	/**
	 * @param mixed $targetPost
	 */
	public function setTargetPost( $targetPost ) {
		$this->targetPost = $targetPost;
	}

	/**
	 * @return mixed
	 */
	public function getClosed() {
		return $this->closed;
	}

	/**
	 * @param mixed $closed
	 */
	public function setClosed( $closed ) {
		$this->closed = $closed;
	}

	public function __construct() {

	}
}
