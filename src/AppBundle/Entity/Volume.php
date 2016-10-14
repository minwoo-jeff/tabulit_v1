<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Volume
 *
 * @ORM\Table(name="volume", indexes={@ORM\Index(name="idx_volume", columns={"serial_id"})})
 * @ORM\Entity
 */
class Volume
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="title", type="string", length=256, nullable=false)    
     */
    private $title;
    
    /**
     * 
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="uploaded_date", type="integer", nullable=false)
     */
    private $uploadedDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=true)
     */
    private $locked;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="smallint", nullable=true)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="overallRating", type="smallint", nullable=true)
     */
    private $overallRating;

    /**
     * @var \AppBundle\Entity\Serial
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Serial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial_id", referencedColumnName="id")
     * })
     */
    private $serial;
    
    /**
     * @var \Doctirne\Common\Collections\Collection
     * 
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Users", mappedBy="purchasedVolumes")
     */
    private $boughtBy;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set uploadedDate
     *
     * @param \DateTime $uploadedDate
     * @return Volume
     */
    public function setUploadedDate($uploadedDate)
    {
        $this->uploadedDate = $uploadedDate;

        return $this;
    }

    /**
     * Get uploadedDate
     *
     * @return \DateTime 
     */
    public function getUploadedDate()
    {
        return $this->uploadedDate;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Volume
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
     * Set price
     *
     * @param integer $price
     * @return Volume
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set serial
     *
     * @param \AppBundle\Entity\Serial $serial
     * @return Volume
     */
    public function setSerial(\AppBundle\Entity\Serial $serial = null)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return \AppBundle\Entity\Serial 
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set overallRating
     *
     * @param integer $overallRating
     * @return Volume
     */
    public function setOverallRating($overallRating)
    {
        $this->overallRating = $overallRating;

        return $this;
    }

    /**
     * Get overallRating
     *
     * @return integer 
     */
    public function getOverallRating()
    {
        return $this->overallRating;
    }

    /**
     * Add boughtBy
     *
     * @param \AppBundle\Entity\Users $boughtBy
     * @return Volume
     */
    public function addBoughtBy(\AppBundle\Entity\Users $boughtBy)
    {
        $this->boughtBy[] = $boughtBy;

        return $this;
    }

    /**
     * Remove boughtBy
     *
     * @param \AppBundle\Entity\Users $boughtBy
     */
    public function removeBoughtBy(\AppBundle\Entity\Users $boughtBy)
    {
        $this->boughtBy->removeElement($boughtBy);
    }

    /**
     * Get boughtBy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBoughtBy()
    {
        return $this->boughtBy;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Volume
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Volume
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}
