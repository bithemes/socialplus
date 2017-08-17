<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Message {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="sentMessages")
	 */
	private $fromUser;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="receivedMessages")
	 */
	private $toUser;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $sendAt;

	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 */
	private $content;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $checked;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Length(max=26)
	 */
	private $username;

	public function __construct() {

	}

	public function getId() {
		return $this->id;
	}

	public function setId( $id ) {
		$this->id = $id;
	}

	public function getFromUser() {
		return $this->fromUser;
	}

	public function setFromUser( $fromUser ) {
		$this->fromUser = $fromUser;
	}

	public function getToUser() {
		return $this->toUser;
	}

	public function setToUser( $toUser ) {
		$this->toUser = $toUser;
	}

	public function getSendAt() {
		return $this->sendAt;
	}

	public function setSendAt( $sendAt ) {
		$this->sendAt = $sendAt;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent( $content ) {
		$this->content = $content;
	}

	public function getChecked() {
		return $this->checked;
	}

	public function setChecked( $checked ) {
		$this->checked = $checked;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername( $username ) {
		$this->username = $username;
	}

}
