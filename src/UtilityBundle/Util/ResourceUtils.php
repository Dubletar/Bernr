<?php

namespace UtilityBundle\Util;

class ResourceUtils
{
    const STREAM = 'stream';
    const HASH_MD5 = 'md5';

    /**
     * Returns true if $object is a resource with a type of STREAM.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public static function isStreamResource($object)
    {
        return !empty($object) && is_resource($object) && self::isResourceType($object, self::STREAM);
    }

    /**
     * Checks $handle for resource type of $type.
     *
     * @param resource $handle
     * @param string   $type
     *
     * @return bool
     */
    public static function isResourceType($handle, $type)
    {
        return get_resource_type($handle) === $type;
    }

    /**
     * Returns contents of stream optionally hased.
     *
     * @param mixed $object Resource with type of stream
     * @param bool  $hash   return hash of resrouce contents
     *
     * @return string|null
     */
    public static function getStreamContent($object, $hash = false)
    {
        if (self::isStreamResource($object)) {
            rewind($object);
            $content = stream_get_contents($object);

            return self::processReturn($content, $hash);
        }

        return;
    }

    /**
     * Returns contents of stream if $object is a stream resource, else the object
     * optionally hashed.
     *
     * @param mixed $object
     * @param bool  $hash
     *
     * @return string|null
     */
    public static function returnStreamContentOrObject($object, $hash = false)
    {
        $content = self::getStreamContent($object, false) ?: $object;

        return self::processReturn($content, $hash);
    }

    /**
     * Helper function to hash return if requested.
     *
     * @param string $string
     * @param bool   $hash
     *
     * @return string
     */
    protected static function processReturn($string, $hash = false)
    {
        return $hash ? hash(self::HASH_MD5, $string) : $string;
    }

    /**
     * @param mixed $resourceA
     * @param mixed $resourceB
     *
     * @return bool
     */
    public static function resourcesAreEqual($resourceA, $resourceB)
    {
        if ($resourceA === $resourceB) {
            return true;
        }

        $hashA = self::returnStreamContentOrObject($resourceA, true);
        $hashB = self::returnStreamContentOrObject($resourceB, true);

        return strlen($hashA) === strlen($hashB) && $hashA === $hashB;
    }
}
