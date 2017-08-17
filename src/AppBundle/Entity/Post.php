<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @Vich\Uploadable
 */
class Post {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts")
	 */
	private $user;

	/**
	 * @ORM\Column(type="text", length=10000)
	 * @Assert\NotBlank()
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $uploadedAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $editedAt;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $imageName;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $imageSize;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $imageUpdatedAt;

	/**
	 * @Assert\File(maxSize="3M")
	 * @Vich\UploadableField(mapping="user_image", fileNameProperty="imageName", size="imageSize")
	 */
	private $imageFile;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="post", cascade={"persist", "remove"})
	 */
	private $comments;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\PostLike", mappedBy="post", cascade={"persist", "remove"})
	 */
	private $likes;

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

	public function getContent() {
		return $this->content;
	}

	public function setContent( $content ) {
		$this->content = $content;
	}

	public function getUploadedAt() {
		return $this->uploadedAt;
	}

	public function setUploadedAt( $uploadedAt ) {
		$this->uploadedAt = $uploadedAt;
	}

	public function getEditedAt() {
		return $this->editedAt;
	}

	public function setEditedAt( $editedAt ) {
		$this->editedAt = $editedAt;
	}

	public function getImageName() {
		return $this->imageName;
	}

	public function setImageName( $imageName ) {
		$this->imageName = $imageName;
	}

	public function getImageSize() {
		return $this->imageSize;
	}

	public function setImageSize( $imageSize ) {
		$this->imageSize = $imageSize;
	}

	public function getImageUpdatedAt() {
		return $this->imageUpdatedAt;
	}

	public function setImageUpdatedAt( $imageUpdatedAt ) {
		$this->imageUpdatedAt = $imageUpdatedAt;
	}

	public function getImageFile() {
		return $this->imageFile;
	}

	public function setImageFile( File $imageFile = null ) {
		$this->imageFile = $imageFile;

		if ($imageFile) {
			$this->imageUpdatedAt = new \DateTimeImmutable();
		}

		return $this;
	}

	public function getComments() {
		return $this->comments;
	}

	public function setComments( $comments ) {
		$this->comments = $comments;
	}

	public function getLikes() {
		return $this->likes;
	}

	public function setLikes( $likes ) {
		$this->likes = $likes;
	}

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Add like
     *
     * @param \AppBundle\Entity\PostLike $like
     *
     * @return Post
     */
    public function addLike(\AppBundle\Entity\PostLike $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like
     *
     * @param \AppBundle\Entity\PostLike $like
     */
    public function removeLike(\AppBundle\Entity\PostLike $like)
    {
        $this->likes->removeElement($like);
    }
}
