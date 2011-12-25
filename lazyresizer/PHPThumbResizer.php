<?php

include_once 'LazyResizer.php';
include_once realpath(__DIR__ .'/../PHPThumb/src/ThumbLib.inc.php');

/**
 * Simple example of usage Lazy Resizer
 * with PHPThumb as image manipulation library
 *
 * @author Aleksandr Gornostal <info@sanya.pp.ua>
 */
class PHPThumbResizer extends LazyResizer
{
    public function resizeAndSave($saveTo, $width, $height)
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