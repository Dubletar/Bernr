<?php

namespace HomeBundle\Controller;

use HomeBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UtilityBundle\Controller\AbstractController;

/**
 * @Route("/editor")
 */
class EditorController extends AbstractController
{
    /**
     * This is the landing page of the site.
     * 
     * @Route("/")
     * 
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('HomeBundle:Editor:index.html.twig');
    }
    
    /**
     * @Route("/write", name="write_an_article", options={"expose":true})
     * 
     * @param Request $request
     * @return Response
     */
    public function writeAction(Request $request)
    {
        // Grab the user.
        $user = $this->isUserLoggedIn();
        // Check if user is logged in.
        if (!($user instanceof User)) {
            return $this->displayAccessDenied();
        }
        
        // Set the parameters.
        $parameters = array('user' => $user);
        // Grab the article manager.
        $articleManager = $this->container->get('editor_article.manager');
        $parameters['article'] = $articleManager->createArticle($user);
        
        return $this->renderTemplate('HomeBundle:Editor:write_an_article.html.twig', $parameters);
    }
    
    /**
     * @Route("/editor-area", name="editor_area", options={"expose":true})
     * 
     * @param Request $request
     * @return Response
     */
    public function editorAreaAction()
    {
        // Grab the user.
        $user = $this->isUserLoggedIn();
        // Check if user is logged in.
        if (!($user instanceof User)) {
            return $this->displayAccessDenied();
        }
        
        // Set the parameters.
        $parameters = array('user' => $user);
        $parameters['articles'] = $this->getEm()->getRepository("HomeBundle:Article")
            ->findBy(array('authorId' => $user));
        
        return $this->renderTemplate('HomeBundle:Editor:editor_area.html.twig', $parameters);
    }
    
    /**
     * getReasonStatementAction.
     *
     * @Route(
     *      "/edit-article/{articleId}",
     *      requirements={"articleId" = "\d+"},
     *      name="edit_article",
     *      options={"expose":true}
     * )
     * @ParamConverter("article", class="HomeBundle:Article", options={"id"="articleId"})
     *
     * @param Article $article
     *
     * @return Response
     */
    public function editArticleAction(Article $article)
    {
        // Grab the user.
        $user = $this->isUserLoggedIn();
        // Check if user is logged in.
        if (!($user instanceof User) || $user != $article->getAuthorId()) {
            return $this->displayAccessDenied();
        }
        
        $parameters = array('user' => $user, 'article' => $article);
        
        return $this->renderTemplate('HomeBundle:Editor:write_an_article.html.twig', $parameters);
    }
    
    /**
     * getReasonStatementAction.
     *
     * @Route(
     *      "/save-article/{articleId}",
     *      requirements={"articleId" = "\d+"},
     *      name="save_article_edits",
     *      options={"expose":true}
     * )
     * @ParamConverter("article", class="HomeBundle:Article", options={"id"="articleId"})
     *
     * @param Article $article
     * @param Request $request
     *
     * @return Response
     */
    public function saveArticleAction(Article $article, Request $request)
    {
        // Grab the user.
        $user = $this->isUserLoggedIn();
        // Check if user is logged in.
        if (!($user instanceof User) || $user != $article->getAuthorId()) {
            return $this->displayAccessDenied();
        }
        
        // Check for post data.
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        
        if (!$title || !$content) {
            return $this->createJmsResponse(false);
        }
        
        // Grab the article manager.
        $articleManager = $this->container->get('editor_article.manager');
        $purifier = $this->container->get('exercise_html_purifier.default');
        $result = $articleManager->saveArticle(
            $article,
            $purifier->purify($title),
            $purifier->purify($content)
        );
        
        return $this->createJmsResponse($result);
    }
    
    /**
     * getReasonStatementAction.
     *
     * @Route(
     *      "/article-images/upload/{articleId}",
     *      requirements={"articleId" = "\d+"},
     *      name="add-article-photos",
     *      options={"expose":true}
     * )
     * @ParamConverter("article", class="HomeBundle:Article", options={"id"="articleId"})
     *
     * @param Article $article
     * @param Request $request
     *
     * @return Response
     */
    public function uploadArticleImagesAction(Article $article, Request $request)
    {
        // Grab the user.
        $user = $this->isUserLoggedIn();
        // Check if user is logged in.
        if (!($user instanceof User) || $user != $article->getAuthorId()) {
            return $this->displayAccessDenied();
        }
        
        $articleManager = $this->container->get('editor_article.manager');
        $result = $articleManager->uploadArticleImages(
            $request->files->get('upload-gallery-photos'),
            $article,
            $user,
            $this->getSitePath()
        );
        
        return $this->createJmsResponse($result);
    }
    
    /**
     * getReasonStatementAction.
     *
     * @Route(
     *      "/article-images/get/{articleId}",
     *      requirements={"articleId" = "\d+"},
     *      name="get-article-photos",
     *      options={"expose":true}
     * )
     * @ParamConverter("article", class="HomeBundle:Article", options={"id"="articleId"})
     *
     * @param Article $article
     * @param Request $request
     *
     * @return Response
     */
    public function getArticleImagesAction(Article $article, Request $request)
    {
        // Grab the user.
        $user = $this->isUserLoggedIn();
        // Check if user is logged in.
        if (!($user instanceof User) || $user != $article->getAuthorId()) {
            return $this->displayAccessDenied();
        }
        
        $imageEntities = $this->getEm()->getRepository("FileBundle:ArticleImage")
            ->findBy(array('article' => $article));
        
        $imageData = array();
        $fileSystemManager = $this->container->get('file_system.manager');
        
        foreach($imageEntities as $image) {
            $imageArray = array(
                'id' => $image->getId(),
                'name' => $image->getOriginalName(),
                'serializedName' => $image->getSerializedName(),
                'url' => $this->getSitePath()
                    . DIRECTORY_SEPARATOR 
                    . $fileSystemManager->getShortFilePath($image->getPath(), $image->getSerializedName())
            );
            $imageData[] = $imageArray;
        }
        
        return $this->createJmsResponse($imageData);
    }
}
