<?php

namespace UserBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractMapImage.
 *
 * @ORM\Table(name="user_profiles")
 * @ORM\Entity(repositoryClass="UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class User
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
     * @var DateTime
     * 
     * @ORM\Column(name="birth_date", type="datetime")
     */
    protected $birthDate;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="gender", type="string", length=50)
     */
    protected $gender;
    
    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=50)
     */
    protected $display;

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
     * @param DateTime $birthDate
     * @return self
     */
    public function setBirthDate(DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }
    
    /**
     * 
     * @param string $gender
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
    
    /**
     * 
     * @param string $displayName
     * @return self
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
}
