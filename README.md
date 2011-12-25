Lazy Resizer
============

Overview
--------

Lazy Resizer is a set of php scripts that provides a convenient way of storing resized images.
It's not a resizing library, but it can use one.

How it works
------------

Script creates a URL of a resized image. But for that moment it does not know
whether resized image exists or not.

When a resized image would be requested and if it exists, image returns to a browser,
otherwise image path rewrites to a php script and resized image will be created.

After resized image was created once, it will never require php script to be executed.

Requirements
------------

* PHP v5.3.0 or higher
* Apache web server with mod_rewrite (or you can use any similar module for your favorite web server)

Installation
------------

* Download this project by issuing a git command `git clone --recursive git://github.com/sanya-gornostal/Lazy-Resizer.git`
or visit a download page
* Configure `imgcache/.htaccess` and `lazyresizer/config.php` for your project
* Extend `LazyResizer` class and implement `resizeAndSave` method 
(or just use `lazyresizer/PHPThumbResizer.php` along with PHPThumb library)
* In `lazyresizer/resize_handler.php` include your class and call a static method `replaceResized`

Usage within a website
----------------------

Example

    PHPThumbResizer::resizedPath($original, 50, 100, array('mode' => 'adaptive'))

* `$original` is a URL to an original image
* `$width` and `$height` is an integer. If you specify one of them it will be fine.
* A third optional parameter is an array of parameters that will be added to a returned URL

In this case returns URL like
    
    imgcache/images/abstract-images/01(81fad6-50x100).jpg?mode=adaptive

P.S.
----

Will be glad to get any advice on how to improve the project. 