<?php

namespace HomeBundle\Service;

use Doctrine\ORM\EntityManager;
use FileBundle\Entity\ArticleImage;
use FileBundle\Service\FileSystemManager;
use HomeBundle\Entity\Article;
use HTMLPurifier;
use UserBundle\Entity\User;

class ArticleManager
{
    /** @var User */
    protected $user;
    
    /** @var EntityManager */
    protected $em;
    
    /** @var FileSystemManager */
    protected $fileSystemManager;
    
    /** @var HTMLPurifier */
    protected $htmlPurifier;
    
    /**
     * 
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager, HTMLPurifier $htmlPurifier, FileSystemManager $fileSystemManager
    ) {
        $this->em = $entityManager;
        $this->fileSystemManager = $fileSystemManager;
        $this->htmlPurifier = $htmlPurifier;
    }
    
    /**
     * Creates an article.
     * @param User $user
     * @return Article
     */
    public function createArticle(User $user)
    {
        $article = new Article();
        $article->setAuthorId($user);
        $this->em->persist($article);
        $this->em->flush();
        
        return $article;
    }
    
    /**
     * 
     * @param Article $article
     * @param string $title
     * @param string $content
     * @return boolean
     */
    public function saveArticle(Article $article, $title, $content)
    {
        try {
            $article->setTitle($title);
            $article->setContent($content);
            $this->em->flush();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function uploadArticleImages(array $images, Article $article, User $user, $sitePath)
    {
        $returnArray = array('success' => array(), 'fail' => array());
        
        foreach ($images as $image) {
            
            $fileType = $this->fileSystemManager->checkFileType($image->getMimeType(), true);
            if ($fileType) {
                
                $imageName = explode('.', $image->getClientOriginalName());
                $fullImageName = $imageName[0] . '.' . $fileType;
                $serializedName = $this->fileSystemManager->serializeFileName($imageName[0], $fileType);
                
                $articleImage = new ArticleImage();
                $articleImage->setOriginalName($fullImageName);
                $articleImage->setSerializedName($serializedName);
                $articleImage->setFileType($fileType);
                $articleImage->setUserId($user);
                $articleImage->setSize($image->getSize());
                $articleImage->setArticle($article);
                
                // Get the path.
                $path = $this->fileSystemManager->buildFilePath(
                    $article->getId(),
                    $this->fileSystemManager->getArticleImageRootType()
                );
                
                if ($path && $image->move($path, $serializedName)) {
                    $articleImage->setPath($path);
                    $this->em->persist($articleImage);
                    
                    $imageArray = array(
                        'name' => $fullImageName,
                        'serialized' => $serializedName,
                        'url' => $sitePath . DIRECTORY_SEPARATOR . $this->fileSystemManager->getShortFilePath($path, $serializedName)
                    );
                    $returnArray['success'][] = $imageArray;
                } else {
                    array_push($returnArray['fail'], $fullImageName);
                }
            }
        }
        
        $this->em->flush();
        
        return $returnArray;
    }
}
