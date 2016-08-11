<?php

namespace HomeBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use UserBundle\Entity\User;

/**
 * AbstractUserData.
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class Article
{
    const EMPTY_TITLE = "Untitled";
    
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
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $authorId;
    
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
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title = self::EMPTY_TITLE;
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=6000)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $content = '';
    
    /**
     * @ORM\OneToMany(targetEntity="FileBundle\Entity\ArticleImage", mappedBy="article", cascade={"remove"})
     * @ORM\OrderBy({"number" = "ASC"})
     */
    protected $images;
    
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
    
    /**
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * 
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title ? $this->title : self::EMPTY_TITLE;
    }
    
    /**
     * 
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
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
    public function getAuthorId()
    {
        return $this->authorId;
    }
    
    /**
     * 
     * @param integer $author
     * @return self
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded->format('M d, Y');
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