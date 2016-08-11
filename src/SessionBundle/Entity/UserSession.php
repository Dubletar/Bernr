<?php

namespace SessionBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * Session.
 *
 * @ORM\Table(name="user_sessions")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks 
 * @JMS\ExclusionPolicy("all")
 */
class UserSession
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
     * @var integer
     * 
     * @ORM\Column(name="valid", type="integer")
     */
    protected $valid = 1;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="keep_alive", type="integer")
     */
    protected $keepAlive = 0;
    
    /**
     * @var blob
     * 
     * @ORM\Column(name="session_id", type="blob")
     */
    protected $sessionId;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="ip_address", type="string", length=50)
     */
    protected $ipAddress;
    
    /**
     * 
     * @return string
     */
    public function getIPAddress()
    {
        return $this->ipAddress;
    }
    
    /**
     * 
     * @param string $ipAddress
     * @return self
     */
    public function setIPAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
    
    /**
     * 
     * @param blob $sessionId
     * @return self
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }
    
    /**
     * 
     * @param integer $keepAlive
     * @return self
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = $keepAlive;
        
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getValid()
    {
        return $this->valid;
    }
    
    /**
     * 
     * @param integer $valid
     * @return self
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        
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
