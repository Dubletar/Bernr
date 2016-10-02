<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Country.
 *
 * @ORM\Table(name="gender")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class Gender
{
    public static $genders = array(
        "Straight Male",
        "Straight Female",
        "Gay Male",
        "Gay Female",
        "Trans Male",
        "Trans Female"
    );

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
     * @ORM\Column(name="name", type="string", length=250)
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_seeking", type="boolean")
     */
    protected $showSeeking;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name string
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getShowSeeking()
    {
        return $this->showSeeking;
    }

    /**
     * @param $showSeeking boolean
     * @return self
     */
    public function setShowSeeking($showSeeking)
    {
        $this->showSeeking = $showSeeking;

        return $this;
    }
}