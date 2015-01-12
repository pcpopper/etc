<?php
/* Options */

$options = (object) array('max' => 32126, 'width' => 250, 'blocked' => 0, 'timed' => 0);

?>
<style>
<?php
if ($options->blocked) {
?>
    .insert {
        background-color: red;
        color: red;
        background-clip: padding-box;
        font-size: 7pt;
        margin: 0.1em;
    }
    .update {
        background-color: #0000FF;
        color: #0000FF;
        background-clip: padding-box;
        font-size: 7pt;
        margin: 0.1em;
    }
    .skipped {
        background-color: forestgreen;
        color: forestgreen;
        background-clip: padding-box;
        font-size: 7pt;
        margin: 0.1em;
}
<?php
} else {
?>
    .insert {
        color: red;
    }
    .update {
        color: #0000FF;
    }
    .skipped {
        color: forestgreen;
    }
<?php
}
?>
    .number_i {
        color: red;
    }
    .number_u {
        color: #0000FF;
    }
    .number_s {
        color: forestgreen;
    }
</style>
<?php
$i = 0;
$numbers = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0);
$row_num = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0);
while ($i < $options->max) {
    if ($i) {
        $num = rand(1,100);
        if ($options->timed) usleep(400000);
        switch ($num) {
            case 1:
            case 2:
            case 3:
                echo "<span class='update' id='$i'>.</span>";
                $numbers->updated++;
                $row_num->updated++;
                break;
            case 10:
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
        if ($i % $options->width == 0) {
            echo "<small> - ".number_format($i)." rows processed. (" . round(($i / $options->max) * 100, 2) . "%) <small><span class='number_s'>".number_format($row_num->skipped)." skipped</span>, " .
                "<span class='number_u'>".number_format($row_num->updated)." updated</span>, <span class='number_i'>".number_format($row_num->added)." added</span>.</small></small><br>";
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
echo "<small> - ".number_format($i)." rows processed. (" . round(($i / $options->max) * 100, 2) . "%) <small><span class='number_s'>".number_format($row_num->skipped)." skipped</span>, " .
    "<span class='number_u'>".number_format($row_num->updated)." updated</span>, <span class='number_i'>".number_format($row_num->added)." added</span>.</small></small>";

echo "<p>Complete! Out of ".number_format($options->max)." rows, <span class='number_s'>".number_format($numbers->skipped)." were skipped</span>, " .
    "<span class='number_u'>".number_format($numbers->updated)." updated</span>, and <span class='number_i'>".number_format($numbers->added)." added</span>.";
?>