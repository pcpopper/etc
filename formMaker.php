<?php
if (!isset($_REQUEST['type']) || !isset($_REQUEST['num']) || !isset($_REQUEST['addr'])) {
    $form = '<form>'
        .'<label>What form type do you want? <select name="type">###</select></label><br>'
        .'<label>How many variables do you want? <input name="num"></label><br>'
        .'<label>What is the address to submit to? <input name="addr"></label><br>'
        .'<input type="submit"></form>';
    $options = array('get','post');
    foreach ($options as $option) {
        $form = str_ireplace('###', "<option value='$option'>$option</option>###", $form);
    }

    echo str_ireplace('###', '', $form);
} else if (!isset($_REQUEST['step'])) {
    $form = '<form method="post">'
        .'<input type="hidden" name="type" value="'.$_REQUEST['type'].'">'
        .'<input type="hidden" name="num" value="'.$_REQUEST['num'].'">'
        .'<input type="hidden" name="addr" value="'.$_REQUEST['addr'].'">'
        .'<input type="hidden" name="step" value="2">'
        .'###'
        .'<input type="submit"></form>';

    for ($i = 1; $i <= intval($_REQUEST['num']); $i++) {
        $form = str_ireplace('###', "<strong>Variable $i:</strong> "
                ."<label>name <input name='name[]'></label> "
                ."<label>value <input name='val[]'></label><br>###", $form);
    }

    echo str_ireplace('###', '', $form);
} else {
    $form = '<form id="myForm" method="'.$_REQUEST['type'].'" action="'.$_REQUEST['addr'].'">###<input type="submit"></form>';

    foreach ($_REQUEST['name'] as $idx=>$name) {
        $val = $_REQUEST['val'][$idx];
        $form = str_ireplace('###', "<input type='hidden' name='$name' value='$val'>###", $form);
    }

    echo str_ireplace('###', '', $form);
    echo '<script>document.getElementById("myForm").submit();</script>';
}