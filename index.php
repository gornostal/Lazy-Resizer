<!doctype html>
<meta charset="utf-8">
<title>Lazy Resizer Demo</title>

<style type="text/css">
    table td {
        text-align: center;
        padding: 20px;
    }
</style>

<?
$original = 'images/abstract-images/01.jpg';
include_once 'resize_handler/PHPThumbResizer.php';
?>

<table>
    <tr>
        <td><img src="<?= $original ?>" /></td>
        <td><img src="<?= PHPThumbResizer::resizedPath($original, 200, 200) ?>" /></td>
        <td><img src="<?= PHPThumbResizer::resizedPath($original, 50, 100, array('mode' => 'adaptive')) ?>" /></td>
    </tr>
    <tr>
        <th>Original</th>
        <th>200x200</th>
        <th>50x100 (adaptive mode)</th>
    </tr>
</table>