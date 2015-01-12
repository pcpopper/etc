<style>
    .insert {
        color: red;
    }
    .update {
        color: #0000FF;
    }
    .skipped {
        color: forestgreen;
    }
</style>
<?php
$i = 0;
$max = 32126;
$width = 250;
$numbers = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0, 'id'=>0);
$row_num = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0);
while ($i < $max) {
    if ($i) {
        $num = rand(1,24);
        //usleep(400000);
        switch ($num) {
            case 4:
            case 5:
                echo "<span class='update' id='$i'>.</span>";
                $numbers->updated++;
                $row_num->updated++;
                break;
            case 6:
                echo "<span class='skipped' id='$i'>.</span>";
                $numbers->skipped++;
                $row_num->skipped++;
                break;
            default:
                echo "<span class='insert' id='$i'>.</span>";
                $numbers->added++;
                $row_num->added++;
                break;
        }
        if ($i % $width == 0) {
            echo "<small> - ".number_format($i)." rows processed. (" . round(($i / $max) * 100, 2) . "%) <small><span class='skipped'>".number_format($row_num->skipped)." skipped</span>, " .
                "<span class='update'>".number_format($row_num->updated)." updated</span>, <span class='insert'>".number_format($row_num->added)." added</span>.</small></small><br>";
            $row_num->added = 0;
            $row_num->updated = 0;
            $row_num->skipped = 0;
            flush();
            ob_flush();
        } else if ($i % 10 == 0) {
            flush();
            ob_flush();
        }
    }
    $i++;
}
echo "<small> - ".number_format($i)." rows processed. (" . round(($i / $max) * 100, 2) . "%) <small><span class='skipped'>".number_format($row_num->skipped)." skipped</span>, " .
    "<span class='update'>".number_format($row_num->updated)." updated</span>, <span class='insert'>".number_format($row_num->added)." added</span>.</small></small>";

echo "<p>Complete! Out of ".number_format($max)." rows, <span class='skipped'>".number_format($numbers->skipped)." were skipped</span>, " .
    "<span class='update'>".number_format($numbers->updated)." updated</span>, and <span class='insert'>".number_format($numbers->added)." added</span>.";
?>