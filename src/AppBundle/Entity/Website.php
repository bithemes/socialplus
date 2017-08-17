<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable()
 */
class Website {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @ORM\Column(type="string")
	 */
	private $subtitle;

	/**
	 * @ORM\Column(type="integer")
	 * @Assert\Length(min="1", max="100")
	 */
	private $posts_per_page;

	/**
	 * @ORM\Column(type="string", length=7)
	 */
	private $main_color;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $brandName;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $brandSize;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $brandUpdatedAt;

	/**
	 * @Assert\File(maxSize="1M")
	 * @Vich\UploadableField(mapping="user_image", fileNameProperty="brandName", size="brandSize")
	 * @Assert\Image(maxHeight="30", maxWidth="180")
	 */
	private $brandFile;

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
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getSubtitle() {
		return $this->subtitle;
	}

	/**
	 * @param mixed $subtitle
	 */
	public function setSubtitle( $subtitle ) {
		$this->subtitle = $subtitle;
	}

	/**
	 * @return mixed
	 */
	public function getPostsPerPage() {
		return $this->posts_per_page;
	}

	/**
	 * @param mixed $posts_per_page
	 */
	public function setPostsPerPage( $posts_per_page ) {
		$this->posts_per_page = $posts_per_page;
	}

	/**
	 * @return mixed
	 */
	public function getMainColor() {
		return $this->main_color;
	}

	/**
	 * @param mixed $main_color
	 */
	public function setMainColor( $main_color ) {
		$this->main_color = $main_color;
	}

	/**
	 * @return mixed
	 */
	public function getBrandName() {
		return $this->brandName;
	}

	/**
	 * @param mixed $brandName
	 */
	public function setBrandName( $brandName ) {
		$this->brandName = $brandName;
	}

	/**
	 * @return mixed
	 */
	public function getBrandSize() {
		return $this->brandSize;
	}

	/**
	 * @param mixed $brandSize
	 */
	public function setBrandSize( $brandSize ) {
		$this->brandSize = $brandSize;
	}

	/**
	 * @return mixed
	 */
	public function getBrandUpdatedAt() {
		return $this->brandUpdatedAt;
	}

	/**
	 * @param mixed $brandUpdatedAt
	 */
	public function setBrandUpdatedAt( $brandUpdatedAt ) {
		$this->brandUpdatedAt = $brandUpdatedAt;
	}

	/**
	 * @return mixed
	 */
	public function getBrandFile() {
		return $this->brandFile;
	}

	/**
	 * @param mixed $brandFile
	 */
	public function setBrandFile( $brandFile ) {
		$this->brandFile = $brandFile;

		if ($brandFile) {
			$this->brandUpdatedAt = new \DateTimeImmutable();
		}

		return $this;
	}

}
