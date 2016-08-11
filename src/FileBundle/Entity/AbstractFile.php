<?php

namespace FileBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractFile.
 *
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks 
 * @JMS\ExclusionPolicy("all")
 */
abstract class AbstractFile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $userId;
    
    /**
     * @var DateTime
     * 
     * @ORM\Column(name="date_added", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $dateAdded;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="file_type", type="string", length=50)
     */
    protected $fileType;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="original_name", type="string", length=1000)
     */
    protected $originalName;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="serialized_name", type="string", length=1000)
     */
    protected $serializedName;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="path", type="string", length=1000)
     */
    protected $path;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="size", type="string", length=100)
     */
    protected $size;
    
    /**
     * 
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * 
     * @param string $size
     * @return self
     */
    public function setSize($size)
    {
        $this->size = $size;
        
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }
    
    /**
     * 
     * @param string $type
     * @return self
     */
    public function setFileType($type)
    {
        $this->fileType = $type;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }
    
    /**
     * 
     * @param string $originalName
     * @return self
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getSerializedName()
    {
        return $this->serializedName;
    }
    
    /**
     * 
     * @param string $serializedName
     * @return self
     */
    public function setSerializedName($serializedName)
    {
        $this->serializedName = $serializedName;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * 
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;
        
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * 
     * @param integer $userId
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
    
    /** 
     * @ORM\PrePersist 
     * 
     * @return self
     */
    public function setDateAdded()
    {
        $this->dateAdded = new DateTime;
        
        return $this;
    }
}
