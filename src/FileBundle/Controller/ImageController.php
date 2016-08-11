<?php

namespace FileBundle\Controller;

use FileBundle\Entity\AbstractFile;
use FileBundle\Entity\ArticleImage;
use FileBundle\Service\FileSystemManager;
use UtilityBundle\Util\ResourceUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UtilityBundle\Controller\AbstractController;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * getReasonStatementAction.
     *
     * @Route(
     *      "/article/view/{serializedName}",
     *      name="view_article_image",
     *      options={"expose":true}
     * )
     * @ParamConverter("image", class="FileBundle:ArticleImage", options={"serializedName"="serializedName"})
     *
     * @param Request $request
     * @param ArticleImage $image
     *
     * @return Response
     */
    public function viewArticleImageAction(Request $request, ArticleImage $image)
    {
        $fileSystemManager = $this->container->get('file_system.manager');
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath()
            . DIRECTORY_SEPARATOR . $fileSystemManager->getShortFilePath($image->getPath(), $image->getSerializedName());
        return $this->createJmsResponse($baseurl);
    }
    
    public function renderImageAction(AbstractFile $image)
    {
        $fileSystemManager = $this->container->get('file_system.manager');
        
        $filePath = $fileSystemManager->getShortFilePath($image->getPath(), $image->getSerializedName());
        
        $fileStream = ResourceUtils::getStreamContent($filePath);

        $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME), $fileStream);
        $imageFileName = $image->getOriginalName();
        $headers = $fileSystemManager->generateFileResponseHeaders($imageFileName, $mimeType);

        return new Response($fileStream, 200, $headers);
    }
}
