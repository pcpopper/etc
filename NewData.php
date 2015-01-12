<?php
/* Options */

$options = (object) array(
    'max' => 32126, // number of dots to process
    'width' => 250, // width of dots on page
    'blocked' => 1, // is dots or blocks
    'block_v' => 2, // block version, currently accepted: 1 and 2
    'timed' => 0, // is time delayed or all at once
    'rand_range' => 100, // random range, 1-n
    'skipped_range' => 1, // number of skipped per rand_range
    'update_range' => 3, // number of updated per rand_range
    'time_delay' => 400000 // delay time
);
?>
<style>
<?php
if ($options->blocked) {
    if ($options->block_v == 1) {
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
    background-color: red;
    color: red;
    border-right: 1px solid black;
    font-size: 9pt;
}
.update {
    background-color: #0000FF;
    color: #0000FF;
    border-right: 1px solid black;
    font-size: 9pt;
}
.skipped {
    background-color: forestgreen;
    color: forestgreen;
    border-right: 1px solid black;
    font-size: 9pt;
}
<?php
    }
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

$ranges = (object) array('skipped_range' => range(1, ($options->skipped_range)), 'update_range' => range(($options->skipped_range + 1), ($options->skipped_range + $options->update_range)));

$i = 0;
$numbers = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0);
$row_num = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0);
while ($i < $options->max) {
    if ($i) {
        $num = rand(1,$options->rand_range);
        if ($options->timed) usleep($options->time_delay);
        if (in_array($num, $ranges->update_range)) {
            echo "<span class='update' id='$i'>.</span>";
            $numbers->updated++;
            $row_num->updated++;
        } else if (in_array($num, $ranges->skipped_range)) {
            echo "<span class='skipped' id='$i'>.</span>";
            $numbers->skipped++;
            $row_num->skipped++;
        } else {
            echo "<span class='insert' id='$i'>.</span>";
            $numbers->added++;
            $row_num->added++;
        }
        if ($i % $options->width == 0) {
            $j++;
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
    "<span class='number_u'>".number_format($numbers->updated)." updated</span>, and <span class='number_i'>".number_format($numbers->added)." added</span>.<script>window.scrollTo(0,document.body.scrollHeight);</script>";
?>