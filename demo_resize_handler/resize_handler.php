<?php

include_once 'PHPThumbResizer.php';

try {
    if ($_GET['path']) {
        PHPThumbResizer::replaceResized($_GET['path']);
    } else {
        throw new Exception('Forbidden', 403);
    }
} catch (Exception $e) {
    $code = $e->getCode() > 0 ? $e->getCode() : 500;
    header('HTTP/1.1 ' . $code);
?>
<!doctype html>
<meta charset="utf-8">
<title>Error <?= $code ?> &mdash; <?= $e->getMessage() ?></title>
<h1>Error <?= $code ?></h1>
<p><?= $e->getMessage() ?></p>
<?
}

