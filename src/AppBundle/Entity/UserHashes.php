<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Users;

/**
 * UserHashes
 *
 * @ORM\Table(name="user_hashes")
 * @ORM\Entity
 */
class UserHashes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="expiry", type="integer", nullable=false)
     */
    private $expiry;

    /**
     * @var string
     *
     * @ORM\Column(name="rc4key", type="string", length=64)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $rc4key;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ip;

    /**
     * @var integer
     *
     * @ORM\Column(name="created", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $created;

    /**
     * @ORM\OneToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     **/
    private $user;

    /**
     * Set expiry
     *
     * @param integer $expiry
     * @return UserHashes
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Get expiry
     *
     * @return integer 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set rc4key
     *
     * @param string $rc4key
     * @return UserHashes
     */
    public function setRc4key($rc4key)
    {
        $this->rc4key = $rc4key;

        return $this;
    }

    /**
     * Get rc4key
     *
     * @return string 
     */
    public function getRc4key()
    {
        return $this->rc4key;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return UserHashes
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return UserHashes
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
     * Set user
     *
     * @param \AppBundle\Entity\Users $user
     * @return UserHashes
     */
    public function setUser(\AppBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
}
