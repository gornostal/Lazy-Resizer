<?php

include_once realpath(__DIR__ . '/../library/LazyResizer/LazyResizer.php');
include_once realpath(__DIR__ .'/../library/PHPThumb/src/ThumbLib.inc.php');

/**
 * Simple example of usage Lazy Resizer
 * with PHPThumb as an image manipulation library
 *
 * @author Aleksandr Gornostal <info@sanya.pp.ua>
 */
class PHPThumbResizer extends LazyResizer
{
    
    /**
     * @return array
     */
    public function loadConfig()
    {
        return include 'config.php';
    }
    
    /**
     *
     * @param string $saveTo Path on server
     * @param int $width Optional if height is specified
     * @param int $height Optional if width is specified
     * @return boolean
     */
    public function resizeAndSave($saveTo, $width, $height = null)
    {
        $thumb = PhpThumbFactory::create($this->getServerPath());
	
        if ($_GET['mode'] == 'adaptive') {
            $thumb->adaptiveResize($width, $height);
        } else {
            $thumb->resize($width, $height);
        }

        return $thumb->save($saveTo);
    }
}