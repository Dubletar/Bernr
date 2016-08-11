<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Password.
 *
 * @ORM\Table(name="user_password")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class Password extends AbstractUserData
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
     * @ORM\Column(name="password", type="string", length=255)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $password;
    
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
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * 
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        
        return $this;
    }
}
