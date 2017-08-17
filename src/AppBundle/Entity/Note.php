<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Note {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="notes")
	 */
	private $user;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $notedAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private $type;

	/**
	 * @ORM\Column(type="text", length=180)
	 * @Assert\NotBlank()
	 * @Assert\Length(min=1,max=180)
	 */
	private $message;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $checked;

	public function __construct() {

	}

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
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser( $user ) {
		$this->user = $user;
	}

	/**
	 * @return mixed
	 */
	public function getNotedAt() {
		return $this->notedAt;
	}

	/**
	 * @param mixed $notedAt
	 */
	public function setNotedAt( $notedAt ) {
		$this->notedAt = $notedAt;
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage( $message ) {
		$this->message = $message;
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

}
