<?php

include_once 'PHPThumbResizer.php';

try {
    if ($_GET['path']) {
        PHPThumbResizer::replaceResized($_GET['path']);
    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo '<!DOCTYPE HTML>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
    <h1>Error</h1>
    <p>'.$e->getMessage().'</p>';
}

