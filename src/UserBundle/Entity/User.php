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
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $lastName;
    
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
     * @ORM\Column(name="username", type="string", length=50)
     */
    protected $username;

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
     * @param string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        
        return $this;
    }
    
    /**
     * 
     * @param string $lastName
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * 
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
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
     * @param string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
