<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Email.
 *
 * @ORM\Table(name="user_email")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class Email extends AbstractUserData
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
     * @ORM\Column(name="email_address", type="string", length=255)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $emailAddress;
    
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
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    
    /**
     * 
     * @param string $emailAddress
     * @return self
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
        
        return $this;
    }
}
