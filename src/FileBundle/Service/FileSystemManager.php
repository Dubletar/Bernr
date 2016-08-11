<?php

namespace FileBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileSystemManager
{   
    const ARTICLE_IMAGE_ROOT = "articleImageRoot";
    const USER_IMAGE_ROOT = "userImageRoot";
    
    /** @var string */
    protected $roots = array(
        self::USER_IMAGE_ROOT => null,
        self::ARTICLE_IMAGE_ROOT => null,
        'web' => null,
        'root' => null,
        'images' => null,
        'uploads' => null
    );
    
    protected $allowImageTypes = array(
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    );
    
    protected $allowedFileTypes = array(
        
    );
    
    /**
     * 
     * @param string $rootDirectory
     */
    public function __construct(Container $container, $rootDirectory)
    {
        $this->container = $container;
        $this->roots['root'] = realpath($rootDirectory . DIRECTORY_SEPARATOR . '../');
        $this->roots['web'] = $this->roots['root'] . DIRECTORY_SEPARATOR . 'web';
        $this->roots['uploads'] = $this->roots['web'] . DIRECTORY_SEPARATOR . 'uploads';
        $this->roots['images'] = $this->roots['uploads'] . DIRECTORY_SEPARATOR . 'images';
        $this->roots['userImageRoot'] = $this->roots['images'] . DIRECTORY_SEPARATOR . 'user';
        $this->roots['articleImageRoot'] = $this->roots['images'] . DIRECTORY_SEPARATOR . 'article';
    }
    
    /**
     * Get the real path of uploaded images.
     * 
     * @param string $filePath
     * @return string
     */
    public function getRealFilePath($rootType, $filePath)
    {
        return array_key_exists($rootType, $this->roots) 
            ? $this->roots[$rootType] . DIRECTORY_SEPARATOR . $filePath
            : false;
    }
    
    public function getShortFilePath($filePath, $fileName)
    {
        return str_replace($this->roots['web'], '', $filePath) . DIRECTORY_SEPARATOR . $fileName;
    }
    
    /**
     * Return path of root directory.
     * 
     * @return string
     */
    public function getRootPath($rootType)
    {   
        return array_key_exists($rootType, $this->roots) ? $this->roots[$rootType] : false;
    }
    
    /**
     * Return the content of the file, after grabbing the real path.
     * 
     * @param string $filePath
     * @return mixed
     */
    public function getFileContent($filePath)
    {
        return file_get_contents($this->getRealMapImagePath($filePath));
    }

    /**
     * Build the file paths of directories and subdirectories
     * @param string $filePath
     * @param string $fileName
     */
    public function buildFilePath($filePath, $rootType = false, $fileName = false)
    {
        $path = ($rootType && array_key_exists($rootType, $this->roots))
            ? $this->roots[$rootType] . DIRECTORY_SEPARATOR . $filePath
            : false;
        
        if ($path) {
            $subdirectories = explode(DIRECTORY_SEPARATOR, $path);
            if (!trim($subdirectories[0])) {
                array_shift($subdirectories);
            }
            
            $thisPath = DIRECTORY_SEPARATOR;
            $keys = array_keys($subdirectories);
            // Loop through array and build the path.
            foreach ($subdirectories as $key => $folder) {
                $addition = $folder . ($key != $keys[count($keys) - 1] ? DIRECTORY_SEPARATOR : '');
                $thisPath .= $addition;
                
                if (!file_exists($thisPath) && !$this->createDirectory($thisPath)) {
                    return false;
                }
            }
            // Return complete path
            $result = $thisPath . ($fileName ? DIRECTORY_SEPARATOR . $fileName : '');
            return $result;
        }
        
        return false;
    }

    /**
     * Creates a directory
     * @param string $directoryPath
     * @return boolean
     */
    public function createDirectory($directoryPath)
    {
        $fileComponent = new Filesystem();
        if (!file_exists($directoryPath)) {
            try {
                $fileComponent->mkdir($directoryPath, 0777);
                return true;
            } catch (IOExceptionInterface $e) {
                //echo "An error occurred while creating your directory at ".$e->getPath();
                return false;
            }
        } else {
            $fileComponent->chmod($directoryPath, 0777);
        }
        return true;
    }
    
    /**
     * Removes all files/folders within a directory and the directory itself.
     * @param string $path
     */
    public function removeFilesAndDirectories($path)
    {
        $files = $this->getAllFoldersFilesFromPath($path);
        foreach ($files as $file) {
            $thisPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($thisPath)) {
                $this->removeFilesAndDirectories($thisPath);
            } else if (is_file($thisPath)) {
                if (!unlink($thisPath)) {
                    return false;
                }
            }
        }
        
        if (is_dir($path)) {
            if (!rmdir($path)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Returns all the files and folders in a path as an array.
     * @param string $path
     * @return array
     */
    public function getAllFoldersFilesFromPath($path)
    {
        return file_exists($path) ? array_diff(scandir($path), array('..', '.')) : false;
    }
    
    public function chmod($directoryPath, $permissions = 0777, $unmask = 0000, $recursive = true)
    {
        $fileComponent = new Filesystem();
        $fileComponent->chmod($directoryPath, $permissions, $unmask, $recursive);
    }
    
    public function copy($fromFile, $toFile, $override = false)
    {
        $fileComponent = new Filesystem();
        $fileComponent->copy($fromFile, $toFile, $override);
    }
    
    public function getUrlPath($path, $packageName = null, $absolute = false, $version = null)
    {
        return $this->container->get('templating.helper.assets')->getUrl($path, $packageName, $version);
    }
    
    public function checkFileType($mimeType, $isImage = false)
    {
        $arrayToCheck = $isImage ? $this->allowImageTypes : $this->allowedFileTypes;
        return in_array($mimeType, $arrayToCheck)
            ? array_search($mimeType, $arrayToCheck)
            : false;
    }
    
    public function serializeFileName($name, $fileType)
    {
        return md5($name . time()) . '.' . $fileType;
    }
    
    public function getArticleImageRootType()
    {
        return self::ARTICLE_IMAGE_ROOT;
    }
    
    public function stripRootFromPath($path)
    {
        return str_replace($this->roots['root'], '', $path);
    }
    
    /**
     * @param string $file_name
     * @param string $type
     */
    public function generateFileResponseHeaders($fileName, $type = null)
    {
        $headers = array(
            'X-Content-Type-Options' => 'nosniff',
        );
        
        if ($type && array_key_exists($type, $this->allowImageTypes)) {
            $headers['Content-Type'] = $this->allowImageTypes[$type];
            $headers['Content-Disposition'] = 'inline; filename="'.$fileName.'"';
        } else {
            $headers['Content-Type'] = 'application/octet-stream';
            $headers['Content-Disposition'] = 'attachment; filename="'.$fileName.'"';
        }

        return $headers;
    }
}
