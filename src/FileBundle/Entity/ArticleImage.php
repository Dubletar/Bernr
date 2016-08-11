<?php

namespace FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FileBundle\Entity\AbstractFile;
use HomeBundle\Entity\Article;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractMapImage.
 *
 * @ORM\Table(name="article_files")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class ArticleImage extends AbstractFile
{
    /**
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Article", inversedBy="images")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $article;
    
    /**
     * 
     * @param Article $article
     * @return self
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
        
        return $this;
    }
    
    /**
     * 
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
