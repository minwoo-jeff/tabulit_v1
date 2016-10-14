<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Users
 *
 * @ORM\Table(name="tabulit_users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class Users implements AdvancedUserInterface, \Serializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, unique=true, nullable=true)
     * 
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password_hash", type="string", length=129, nullable=false)
     * @Assert\NotBlank(message="Password may not be empty")
     * @Assert\Length(
     *      min = "8",
     *      minMessage = "Password must be at least 8 characters long",
     *      groups = {"Default"}
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="password_salt", type="string", length=22, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=25, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=25, nullable=false)
     */
    private $lastName;

    /**
     * @var integer
     *
     * @ORM\Column(name="gender", type="smallint", nullable=false)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="date", nullable=false)
     */
    private $birthDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_verified", type="boolean", nullable=false)
     */
    private $emailVerified;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Roles")
     * @ORM\JoinTable(name="user_roles")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="locked_ip", type="string", length=45, nullable=true)
     */
    private $lockedIp;

    /**
     * @var string
     *
     * @ORM\Column(name="last_ip", type="string", length=45, nullable=false)
     */
    private $lastIp;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=false)
     */
    private $locked;

    /**
     * @var integer
     *
     * @ORM\Column(name="failed_attempts", type="smallint", nullable=false)
     */
    private $failedAttempts;

    /**
     * @var integer
     *
     * @ORM\Column(name="created", type="integer", nullable=false)
     */
    private $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_login", type="integer", nullable=false)
     */
    private $lastLogin;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="coin", type="integer", nullable=false)
     */
    private $coin;

    
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Serial", mappedBy="writtenBy")
     **/
    private $mySerials;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Serial", inversedBy="favoriteUser")
     * @ORM\JoinTable(name="user_favorite_serial",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="serial_id", referencedColumnName="id")
     *   }
     * )
     */
    private $favoriteSerials;
    
    /**
     *
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Volume", inversedBy="boughtBy")
     * @ORM\JoinTable(
     *  name="user_volumes_purchased",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="volume_id", referencedColumnName="id")}
     * )
     */
    private $purchasedVolumes;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="t_accepted", type="smallint")
     */
    private $accepted;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->emailVerified = false;
        $this->locked = false;
        $this->failedAttempts = 0;
        $this->salt = md5(uniqid("p_", true) . time());
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $roles = $this->roles->toArray();
        return $roles;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->locked,
            $this->emailVerified,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->locked,
            $this->emailVerified
        ) = unserialize($serialized);
    }
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        //return !$this->locked;
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        //return $this->emailVerified;
        return true;
    }


    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Users
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Users
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     * @return Users
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return Users
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set emailVerified
     *
     * @param boolean $emailVerified
     * @return Users
     */
    public function setEmailVerified($emailVerified)
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    /**
     * Get emailVerified
     *
     * @return boolean 
     */
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * Set lockedIp
     *
     * @param string $lockedIp
     * @return Users
     */
    public function setLockedIp($lockedIp)
    {
        $this->lockedIp = $lockedIp;

        return $this;
    }

    /**
     * Get lockedIp
     *
     * @return string 
     */
    public function getLockedIp()
    {
        return $this->lockedIp;
    }

    /**
     * Set lastIp
     *
     * @param string $lastIp
     * @return Users
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;

        return $this;
    }

    /**
     * Get lastIp
     *
     * @return string 
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Users
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set failedAttempts
     *
     * @param integer $failedAttempts
     * @return Users
     */
    public function setFailedAttempts($failedAttempts)
    {
        $this->failedAttempts = $failedAttempts;

        return $this;
    }

    /**
     * Get failedAttempts
     *
     * @return integer 
     */
    public function getFailedAttempts()
    {
        return $this->failedAttempts;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return Users
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set lastLogin
     *
     * @param integer $lastLogin
     * @return Users
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return integer 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set coin
     *
     * @param integer $coin
     * @return Users
     */
    public function setCoin($coin)
    {
        $this->coin = $coin;

        return $this;
    }

    /**
     * Get coin
     *
     * @return integer 
     */
    public function getCoin()
    {
        return $this->coin;
    }
   

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add roles
     *
     * @param \AppBundle\Entity\Roles $roles
     * @return Users
     */
    public function addRole(\AppBundle\Entity\Roles $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \AppBundle\Entity\Roles $roles
     */
    public function removeRole(\AppBundle\Entity\Roles $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Add mySerials
     *
     * @param \AppBundle\Entity\Serial $mySerials
     * @return Users
     */
    public function addMySerial(\AppBundle\Entity\Serial $mySerials)
    {
        $this->mySerials[] = $mySerials;

        return $this;
    }

    /**
     * Remove mySerials
     *
     * @param \AppBundle\Entity\Serial $mySerials
     */
    public function removeMySerial(\AppBundle\Entity\Serial $mySerials)
    {
        $this->mySerials->removeElement($mySerials);
    }

    /**
     * Get mySerials
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMySerials()
    {
        return $this->mySerials;
    }

    /**
     * Add favoriteSerials
     *
     * @param \AppBundle\Entity\Serial $favoriteSerials
     * @return Users
     */
    public function addFavoriteSerial(\AppBundle\Entity\Serial $favoriteSerials)
    {
        $this->favoriteSerials[] = $favoriteSerials;

        return $this;
    }

    /**
     * Remove favoriteSerials
     *
     * @param \AppBundle\Entity\Serial $favoriteSerials
     */
    public function removeFavoriteSerial(\AppBundle\Entity\Serial $favoriteSerials)
    {
        $this->favoriteSerials->removeElement($favoriteSerials);
    }

    /**
     * Get favoriteSerials
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavoriteSerials()
    {
        return $this->favoriteSerials;
    }

    /**
     * Add purcahsedVolumes
     *
     * @param \AppBundle\Entity\Volume $purcahsedVolumes
     * @return Users
     */
    public function addPurchasedVolume(\AppBundle\Entity\Volume $purchasedVolumes)
    {
        $this->purchasedVolumes[] = $purchasedVolumes;

        return $this;
    }

    /**
     * Remove purchasedVolumes
     *
     * @param \AppBundle\Entity\Volume $purchasedVolumes
     */
    public function removePurchasedVolume(\AppBundle\Entity\Volume $purchasedVolumes)
    {
        $this->purchasedVolumes->removeElement($purchasedVolumes);
    }

    /**
     * Get purchasedVolumes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPurchasedVolumes()
    {
        return $this->purchasedVolumes;
    }

    /**
     * Set accepted
     *
     * @param integer $accepted
     * @return Users
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return integer 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }
}
