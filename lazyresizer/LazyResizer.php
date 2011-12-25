<?php

/**
 * Lazy Resizer
 * 
 * @author Aleksandr Gornostal <info@sanya.pp.ua>
 * @license WTFPL v2
 */
abstract class LazyResizer
{
    /**
     * Path to original image
     *
     * @var string
     */
    protected $_path;
        
    /**
     *
     * @param string $path Path to the original file relative to $_documentRoot
     * @throws InvalidArgumentException
     */
    public function __construct($path)
    {
        $this->_path = preg_replace('/^\/+/', '', $path);
        if (!$this->_path) {
            throw new InvalidArgumentException('You did not specify either width or height');
        }
    }
    
    public static function getConfig($name)
    {
        static $config;
        
        if (!$config) {
            $config = include __DIR__ .'/config.php';
            if (!is_array($config)) {
                throw new Exception('config.php must return an array');
            }
        }
        
        if (!isset($config[$name])) {
            throw new Exception("Parameter '$name' is not defined in config");
        }
        
        return $config[$name];
    }
    
    /**
     * Full path to original file
     *
     * @return string
     */
    public function getServerPath()
    {
        return self::getConfig('documentRoot') . DIRECTORY_SEPARATOR . $this->_path;
    }

    /**
     * Returns true in case resized file was successfully created
     * 
     * @param $saveTo Full path to resized file
     * @param int $width
     * @param int $height
     * @return boolean
     */
    abstract public function resizeAndSave($saveTo, $width, $height);
    
    /**
     * Returns path to resized image
     *
     * @param int $width
     * @param int $height
     * @param int $params
     * @return string
     * @throws Exception
     */
    public function getResizedPath($width = 0, $height = 0, $params = array())
    {
        $width = intval($width);
        $height = intval($height);
        if (!$width && !$height) {
            throw new InvalidArgumentException('You did not specify either width or height');
        }
        
        if (!file_exists($this->getServerPath())) {
            throw new Exception("File '{$this->getServerPath()}' not found on the server");
        }
        
        $query = '';
        if (is_array($params) && count($params)) {
            $parts = array();
            foreach ($params as $k => $v) {
                $parts[] = $k . '=' . $v;
            }
            $query = '?' . implode('&', $parts);
        }
        
        $checksum = substr(md5(md5_file($this->getServerPath()) . 
                $width . $height . $query), 0, 6);
        
        $newPath = preg_replace('|^(.*)([^/]*)(\.?[^/.]*)$|U', 
                sprintf('$1$2(%s-%sx%s)$3', $checksum, $width, $height), $this->_path);
        
        return self::getConfig('cacheUrl') . '/' . $newPath . $query;
    }

    /**
     * Returns path to resized image
     *
     * @param string $path Path to original image
     * @param int $width
     * @param int $height
     * @param array $params [optional] Additional params will be added to query string
     * @return string
     */
    public static function resizedPath($path, $width = 0, $height = 0, $params = array())
    {
        $resizer = new static($path);
        return $resizer->getResizedPath($width, $height, $params);
    }
    
    /**
     * Invokes resizing process
     *
     * @param string $request Path to resized file
     * @throws Exception
     */
    public static function replaceResized($request)
    {
        if (preg_match('/^(.+)\(([a-z0-9]{6})-(\d*)x(\d*)\)(\.?[a-z]*)$/i', $request, $matches)) {
            $image = $matches[1];
            $checksum = $matches[2];
            $width = intval($matches[3]);
            $height = intval($matches[4]);
            $ext = $matches[5];
            
            if (!$width && !$height) {
                throw new InvalidArgumentException('Neither width or height was specified');
            }

            $path = $image . $ext;
            $original = self::getConfig('documentRoot') . DIRECTORY_SEPARATOR . $path;

            if (file_exists($original)) {
                
                // check the checksum
                $query = '';
                if (count($_GET)) {
                    $parts = array();
                    foreach ($_GET as $k => $v) {
                        if ($v == $request) {
                            continue;
                        }
                        $parts[] = $k . '=' . $v;
                    }
                    $query = count($parts) ? '?' . implode('&', $parts) : '';
                }
                if ($checksum != substr(md5(md5_file($original) . 
                        $width . $height . $query), 0, 6)) {
                    throw new Exception('The file checksum does not match the coumputed checksum');
                }
                
                $resizer = new static($path);
                $saveTo = self::getConfig('documentRoot'). DIRECTORY_SEPARATOR . 
                        self::getConfig('cacheUrl') . DIRECTORY_SEPARATOR . $request;
                
                $saveToDir = dirname($saveTo);
                if (!file_exists($saveToDir)) {
                    mkdir($saveToDir, 0777, true);
                }
                
                if ($resizer->resizeAndSave($saveTo, $width, $height)) {
                    $info = getimagesize($saveTo);
                    $fp = fopen($saveTo, "rb");
                    if ($info && $fp) {
                        header("Content-type: {$info['mime']}");
                        fpassthru($fp);
                        exit;
                    } else {
                        throw new Exception("Cannot read from file '$path'");
                    }
                } else {
                    throw new Exception("File '$request' was not saved");
                }
            } else {
                throw new Exception("File '$path' was not found on this server");
            }
        } else {
            throw new Exception('Invalid image URL');
        }
    }
    
}