<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Serial
 *
 * @ORM\Table(name="serial")
 * @ORM\Entity
 */
class Serial
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
     * @ORM\Column(name="title", type="string", length=75, nullable=false, unique=true)    
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=256, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="integer", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="integer", nullable=true)
     */
    private $editedOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Volume", mappedBy="serial")
     **/
    private $volumes;

    /**
     * @var \AppBundle\Entity\Writer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Users", inversedBy="mySerials")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="writers", referencedColumnName="id")
     * })
     */
    private $writtenBy;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Users", mappedBy="favoriteSerials")
     */
    private $favoriteUser;
    
    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=10, nullable=true)
     */
    private $category;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $filepath;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
    
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
     * Set title
     *
     * @param string $title
     * @return Serial
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
     * Set description
     *
     * @param string $description
     * @return Serial
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Serial
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime 
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Serial
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add volumes
     *
     * @param \AppBundle\Entity\Volume $volumes
     * @return Serial
     */
    public function addVolume(\AppBundle\Entity\Volume $volumes)
    {
        $this->volumes[] = $volumes;

        return $this;
    }

    /**
     * Remove volumes
     *
     * @param \AppBundle\Entity\Volume $volumes
     */
    public function removeVolume(\AppBundle\Entity\Volume $volumes)
    {
        $this->volumes->removeElement($volumes);
    }

    /**
     * Get volumes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * Set writtenBy
     *
     * @param \AppBundle\Entity\Users $writtenBy
     * @return Serial
     */
    public function setWrittenBy(\AppBundle\Entity\Users $writtenBy = null)
    {
        $this->writtenBy = $writtenBy;

        return $this;
    }

    /**
     * Get writtenBy
     *
     * @return \AppBundle\Entity\Users 
     */
    public function getWrittenBy()
    {
        return $this->writtenBy;
    }

    /**
     * Add favoriteUser
     *
     * @param \AppBundle\Entity\Users $favoriteUser
     * @return Serial
     */
    public function addFavoriteUser(\AppBundle\Entity\Users $favoriteUser)
    {
        $this->favoriteUser[] = $favoriteUser;

        return $this;
    }

    /**
     * Remove favoriteUser
     *
     * @param \AppBundle\Entity\Users $favoriteUser
     */
    public function removeFavoriteUser(\AppBundle\Entity\Users $favoriteUser)
    {
        $this->favoriteUser->removeElement($favoriteUser);
    }

    /**
     * Get favoriteUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavoriteUser()
    {
        return $this->favoriteUser;
    }

    /**
     * Set editedOn
     *
     * @param integer $editedOn
     * @return Serial
     */
    public function setEditedOn($editedOn)
    {
        $this->editedOn = $editedOn;

        return $this;
    }

    /**
     * Get editedOn
     *
     * @return integer 
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Serial
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->filepath
            ? null
            : $this->getUploadRootDir().'/'.$this->filepath;
    }

    public function getWebPath()
    {
        return null === $this->filepath
            ? null
            : $this->getUploadDir().'/'.$this->filepath;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'bundles/app/uploads';
    }
    
    public function upload()
{
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }
        
        $this->getFile()->move(
            $this->getUploadRootDir(),
            time() . '_' . $this->getFile()->getClientOriginalName()
        );
        
        // set the path property to the filename where you've saved the file
        $this->filepath = time() . '_' . $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }
    
    public function remove()
    {
        if ($this->getFile() === null) {
            return;
        }
        $this->getFile()->unlink($this->getUploadRootDir(), $this->filepath);
        $this->file = null;
    }

    /**
     * Set filepath
     *
     * @param string $filepath
     * @return Serial
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * Get filepath
     *
     * @return string 
     */
    public function getFilepath()
    {
        return $this->filepath;
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }


    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
}
