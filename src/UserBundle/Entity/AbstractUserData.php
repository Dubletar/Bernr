<?php

namespace UserBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractUserData.
 *
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks 
 * @JMS\ExclusionPolicy("all")
 */
abstract class AbstractUserData
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
     * @ORM\ManyToOne(targetEntity="User")
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
     * @var integer
     * 
     * @ORM\Column(name="current", type="integer")
     */
    protected $current = 1;
    
    /**
     * 
     * @return integer
     */
    public function getCurrent()
    {
        return $this->current;
    }
    
    /**
     * 
     * @param integer $current
     * @return integer
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        
        return $this->current;
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
