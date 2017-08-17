<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Comment {
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments")
	 */
	private $user;

	/**
	 * @ORM\Column(type="text", length=1600)
	 * @Assert\NotBlank()
	 * @Assert\Length(max="1600", maxMessage="Maximum acceptable comment length is 1600 characters.")
	 */
	private $content;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $commentedAt;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\CommentLike", mappedBy="comment", cascade={"persist", "remove"})
	 */
	private $likes;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", inversedBy="childComments")
	 * @ORM\JoinColumn(name="parent_comment_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $parentComment;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="parentComment", cascade={"persist", "remove"})
	 */
	private $childComments;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="comments")
	 */
	private $post;

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

	public function getCommentedAt() {
		return $this->commentedAt;
	}

	public function setCommentedAt( $commentedAt ) {
		$this->commentedAt = $commentedAt;
	}

	public function getLikes() {
		return $this->likes;
	}

	public function setLikes( $likes ) {
		$this->likes = $likes;
	}

	public function getParentComment() {
		return $this->parentComment;
	}

	public function setParentComment( $parentComment ) {
		$this->parentComment = $parentComment;
	}

	public function getPost() {
		return $this->post;
	}

	public function setPost( $post ) {
		$this->post = $post;
	}

	public function getChildComments() {
		return $this->childComments;
	}

	public function setChildComments( $childComments ) {
		$this->childComments = $childComments;
	}


    /**
     * Add like
     *
     * @param \AppBundle\Entity\CommentLike $like
     *
     * @return Comment
     */
    public function addLike(\AppBundle\Entity\CommentLike $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like
     *
     * @param \AppBundle\Entity\CommentLike $like
     */
    public function removeLike(\AppBundle\Entity\CommentLike $like)
    {
        $this->likes->removeElement($like);
    }

    /**
     * Add childComment
     *
     * @param \AppBundle\Entity\Comment $childComment
     *
     * @return Comment
     */
    public function addChildComment(\AppBundle\Entity\Comment $childComment)
    {
        $this->childComments[] = $childComment;

        return $this;
    }

    /**
     * Remove childComment
     *
     * @param \AppBundle\Entity\Comment $childComment
     */
    public function removeChildComment(\AppBundle\Entity\Comment $childComment)
    {
        $this->childComments->removeElement($childComment);
    }
}
