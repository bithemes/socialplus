<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="This username is already in use.")
 * @UniqueEntity(fields="email", message="This email is already in use.")
 * @Vich\Uploadable
 */
class User implements UserInterface, \Serializable {

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=26, unique=true)
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Length(max=26)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Email()
	 */
	private $email;

	/**
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Length(max=64)
	 */
	private $plainPassword;

	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=40)
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Length(min=1, max=40)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=40)
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Length(min=1, max=40)
	 */
	private $surname;

	/**
	 * @ORM\Column(type="boolean")
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Choice({"0","1"})
	 */
	private $gender;

	/**
	 * @ORM\Column(type="string", length=2)
	 * @Assert\NotBlank(groups={"register"})
	 * @Assert\Country()
	 */
	private $country;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @ORM\Column(type="array")
	 */
	private $roles;

	/**
	 * @ORM\Column(type="datetime")
	 * @Assert\DateTime()
	 */
	private $joined;

	/**
	 * @ORM\Column(type="string", length=80)
	 * @Assert\Length(max=80)
	 */
	private $description;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $location;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @Assert\Url()
	 */
	private $website;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $workplace;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $profession;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $school;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $birthday;

	/**
	 * @ORM\Column(type="string", length=2)
	 */
	private $language;


	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $backgroundName;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $backgroundSize;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $backgroundUpdatedAt;

	/**
	 * @Assert\File(maxSize="3M")
	 * @Vich\UploadableField(mapping="user_image", fileNameProperty="backgroundName", size="backgroundSize")
	 */
	private $backgroundFile;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $avatarName;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $avatarSize;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $avatarUpdatedAt;

	/**
	 * @Assert\File(maxSize="1M")
	 * @Vich\UploadableField(mapping="user_image", fileNameProperty="avatarName", size="avatarSize")
	 */
	private $avatarFile;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $posts;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $comments;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Follow", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $follows; // who we follow

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Follow", mappedBy="targetUser", cascade={"persist", "remove"})
	 */
	private $followers; // who follows us

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="fromUser", cascade={"persist", "remove"})
	 */
	private $sentMessages;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="toUser", cascade={"persist", "remove"})
	 */
	private $receivedMessages;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Note", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $notes;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ban", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $bans;

	public function __construct() {
		$this->isActive = true;
		$this->roles = ['ROLE_USER'];
		$this->joined = new \DateTime();
		$this->description = 'This is a simple, short description of your profile. Feel free to edit this.';
		$this->language = 'en';
	}

	public function getId() {
		return $this->id;
	}

	public function setId( $id ) {
		$this->id = $id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername( $username ) {
		$this->username = $username;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail( $email ) {
		$this->email = $email;
	}

	public function getPlainPassword() {
		return $this->plainPassword;
	}

	public function setPlainPassword( $plainPassword ) {
		$this->plainPassword = $plainPassword;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword( $password ) {
		$this->password = $password;
	}

	public function getIsActive() {
		return $this->isActive;
	}

	public function setIsActive( $isActive ) {
		$this->isActive = $isActive;
	}

	public function getRoles() {
		return $this->roles;
	}

	public function setRoles( $roles ) {
		$this->roles = $roles;
	}

	public function getName() {
		return $this->name;
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function getSurname() {
		return $this->surname;
	}

	public function setSurname( $surname ) {
		$this->surname = $surname;
	}

	public function getGender() {
		return $this->gender;
	}

	public function setGender( $gender ) {
		$this->gender = $gender;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setCountry( $country ) {
		$this->country = $country;
	}

	public function getJoined() {
		return $this->joined;
	}

	public function setJoined( $joined ) {
		$this->joined = $joined;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription( $description ) {
		$this->description = $description;
	}

	public function getLocation() {
		return $this->location;
	}

	public function setLocation( $location ) {
		$this->location = $location;
	}

	public function getWebsite() {
		return $this->website;
	}

	public function setWebsite( $website ) {
		$this->website = $website;
	}

	public function getWorkplace() {
		return $this->workplace;
	}

	public function setWorkplace( $workplace ) {
		$this->workplace = $workplace;
	}

	public function getProfession() {
		return $this->profession;
	}

	public function setProfession( $profession ) {
		$this->profession = $profession;
	}

	public function getSchool() {
		return $this->school;
	}

	public function setSchool( $school ) {
		$this->school = $school;
	}

	public function getBirthday() {
		return $this->birthday;
	}

	public function setBirthday( $birthday ) {
		$this->birthday = $birthday;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function setLanguage( $language ) {
		$this->language = $language;
	}

	public function getBackgroundUpdatedAt() {
		return $this->backgroundUpdatedAt;
	}

	public function setBackgroundUpdatedAt( $backgroundUpdatedAt ) {
		$this->backgroundUpdatedAt = $backgroundUpdatedAt;
	}

	public function getAvatarUpdatedAt() {
		return $this->avatarUpdatedAt;
	}

	public function setAvatarUpdatedAt( $avatarUpdatedAt ) {
		$this->avatarUpdatedAt = $avatarUpdatedAt;
	}

	public function getBackgroundName() {
		return $this->backgroundName;
	}

	public function setBackgroundName( $backgroundName ) {
		$this->backgroundName = $backgroundName;
	}

	public function getBackgroundSize() {
		return $this->backgroundSize;
	}

	public function setBackgroundSize( $backgroundSize ) {
		$this->backgroundSize = $backgroundSize;
	}

	public function getBackgroundFile() {
		return $this->backgroundFile;
	}

	public function setBackgroundFile( File $backgroundFile = null ) {
		$this->backgroundFile = $backgroundFile;

		if ($backgroundFile) {
			$this->backgroundUpdatedAt = new \DateTimeImmutable();
		}

		return $this;
	}

	public function getAvatarName() {
		return $this->avatarName;
	}

	public function setAvatarName( $avatarName ) {
		$this->avatarName = $avatarName;
	}

	public function getAvatarSize() {
		return $this->avatarSize;
	}

	public function setAvatarSize( $avatarSize ) {
		$this->avatarSize = $avatarSize;
	}

	public function getAvatarFile() {
		return $this->avatarFile;
	}

	public function setAvatarFile( File $avatarFile = null ) {
		$this->avatarFile = $avatarFile;

		if ($avatarFile) {
			$this->avatarUpdatedAt = new \DateTimeImmutable();
		}

		return $this;
	}

	public function getPosts() {
		return $this->posts;
	}

	public function setPosts( $posts ) {
		$this->posts = $posts;
	}

	public function getComments() {
		return $this->comments;
	}

	public function setComments( $comments ) {
		$this->comments = $comments;
	}

	public function getFollows() {
		return $this->follows;
	}

	public function setFollows( $follows ) {
		$this->follows = $follows;
	}

	public function getFollowers() {
		return $this->followers;
	}

	public function setFollowers( $followers ) {
		$this->followers = $followers;
	}

	public function getSentMessages() {
		return $this->sentMessages;
	}

	public function setSentMessages( $sentMessages ) {
		$this->sentMessages = $sentMessages;
	}

	public function getReceivedMessages() {
		return $this->receivedMessages;
	}

	public function setReceivedMessages( $receivedMessages ) {
		$this->receivedMessages = $receivedMessages;
	}

	public function getNotes() {
		return $this->notes;
	}

	public function setNotes( $notes ) {
		$this->notes = $notes;
	}

	public function serialize() {
		return serialize([
			$this->id,
			$this->username,
			$this->password
		]);
	}

	public function unserialize( $serialized ) {
		list($this->id,
			$this->username,
			$this->password
			) = unserialize($serialized);
	}

	public function getSalt() {
		return null;
	}

	public function eraseCredentials() {

	}

    /**
     * Add post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return User
     */
    public function addPost(\AppBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \AppBundle\Entity\Post $post
     */
    public function removePost(\AppBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
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
     * Add follow
     *
     * @param \AppBundle\Entity\Follow $follow
     *
     * @return User
     */
    public function addFollow(\AppBundle\Entity\Follow $follow)
    {
        $this->follows[] = $follow;

        return $this;
    }

    /**
     * Remove follow
     *
     * @param \AppBundle\Entity\Follow $follow
     */
    public function removeFollow(\AppBundle\Entity\Follow $follow)
    {
        $this->follows->removeElement($follow);
    }

    /**
     * Add follower
     *
     * @param \AppBundle\Entity\Follow $follower
     *
     * @return User
     */
    public function addFollower(\AppBundle\Entity\Follow $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower
     *
     * @param \AppBundle\Entity\Follow $follower
     */
    public function removeFollower(\AppBundle\Entity\Follow $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Add sentMessage
     *
     * @param \AppBundle\Entity\Message $sentMessage
     *
     * @return User
     */
    public function addSentMessage(\AppBundle\Entity\Message $sentMessage)
    {
        $this->sentMessages[] = $sentMessage;

        return $this;
    }

    /**
     * Remove sentMessage
     *
     * @param \AppBundle\Entity\Message $sentMessage
     */
    public function removeSentMessage(\AppBundle\Entity\Message $sentMessage)
    {
        $this->sentMessages->removeElement($sentMessage);
    }

    /**
     * Add receivedMessage
     *
     * @param \AppBundle\Entity\Message $receivedMessage
     *
     * @return User
     */
    public function addReceivedMessage(\AppBundle\Entity\Message $receivedMessage)
    {
        $this->receivedMessages[] = $receivedMessage;

        return $this;
    }

    /**
     * Remove receivedMessage
     *
     * @param \AppBundle\Entity\Message $receivedMessage
     */
    public function removeReceivedMessage(\AppBundle\Entity\Message $receivedMessage)
    {
        $this->receivedMessages->removeElement($receivedMessage);
    }

	/**
	 * @return mixed
	 */
	public function getBans() {
		return $this->bans;
	}

	/**
	 * @param mixed $bans
	 */
	public function setBans( $bans ) {
		$this->bans = $bans;
	}

}
