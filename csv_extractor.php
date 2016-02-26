<form method="get">
    <input type="text" id="filename" name="filename">
    <input type="submit" value="Start">
</form>
<pre>

<?php


if ($_GET['filename']) {
    require_once('csv_extractor/' . $_GET['filename'] . '.php');
    $className = $_GET['filename'];
    new $className($_GET['filename']);
}