<?php
if (isset($_POST['json'])) {
    header('Content-Type: application/json');
    echo $_POST['json'];
} else {
?>
    <form method="post">
        <textarea name="json" style="width: 100%;height: 50%;"></textarea>
        <p>
        <input type="submit" value="submit">
    </form>
<?php
}