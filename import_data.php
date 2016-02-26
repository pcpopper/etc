<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/* Options */

$options = (object) array(
    'max' => 32126, // number of dots to process
    'width' => 250, // width of dots on page
    'blocked' => 1, // is dots or blocks
    'block_v' => 2, // block version, currently accepted: 1 and 2
    'timed' => 0, // is time delayed or all at once
    'rand_range' => 100, // random range, 1-n
    'skipped_range' => 0, // number of skipped per rand_range
    'update_range' => 1, // number of updated per rand_range
    'time_delay' => 400000 // delay time
);
$cols = (object) array(
    'ModelNumber'=>'',
    'Barcode'=>'',
    'Manufacturer'=>'',
    'Procedure'=>'TKA',
    'ImplantType'=>'undefined',
    'Name'=>'',
    'Details'=>'',
    'ImplantLine'=>'undefined',
    'CatalogNumber'=>''
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
            background-color: orange;
            color: orange;
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
            color: <?php echo ($options->block_v == 2) ? "orange" : "blue" ?>;
        }
        .number_s {
            color: forestgreen;
        }
    </style>
<form method="post">
    <input type="file" name="file">
    <input type="submit" value="Submit">
</form>
<p><hr></p>
<?php
if (isset($_POST['file']) && !isset($_POST['bound'])) {
    $csv = array_map('str_getcsv', file($_POST['file']));

    $columnsOut = array();
    foreach ($csv[0] as $idx => $column) {
        $columnsOut[] = "<option value=\"$idx\">$column</option>";
    }
    $columnsOut[] = "<option value=\"other\">Other</option>";

    $selectsOut = array();
    $columns = implode("\n", $columnsOut);
    foreach ($cols as $k=>$v) {
        $selectsOut[] = "<label>$k: <select name=\"$k\">$columns</select></label> <input type=\"text\" name=\"".$k."_other\" value=\"$v\">";
    }

    echo "<form method=\"post\">"
        ."<input type=\"hidden\" name=\"file\" value=\"".$_POST['file']."\">"
        ."<input type=\"hidden\" name=\"bound\" value=\"true\">"
        .implode("<br>\n", $selectsOut)."<br>"
        ."<input type=\"submit\" value=\"Sumbit\">"
        ."</form>";
} if (isset($_POST['file']) && isset($_POST['bound'])) {
    $csv = array_map('str_getcsv', file($_POST['file']));
    $options->max = count($csv);

    $host = '10.45.1.100';
    $username = 'admin';
    $password = 'max2lab';
    $port = '3307';
    $db = 'medadatonline_demo';

    // Create connection
    $conn = new mysqli($host, $username, $password, $db, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $count = count($csv) - 1;
    $numbers = (object)array('skipped' => 0, 'updated' => 0, 'added' => 0, 'id' => 0);
    $row_num = (object)array('skipped' => 0, 'updated' => 0, 'added' => 0);
    $width = 250;

    $i = 0;
    foreach ($csv as $item) {
        if ($i) {
            $modelNumber = ($_POST['ModelNumber'] == 'other') ? trim($_POST['ModelNumber_other']) : trim($item[$_POST['ModelNumber']]);
            $barcode = ($_POST['Barcode'] == 'other') ? trim($_POST['Barcode_other']) : trim($item[$_POST['Barcode']]);
            $manufacturer = ($_POST['Manufacturer'] == 'other') ? trim($_POST['Manufacturer_other']) : trim($item[$_POST['Manufacturer']]);
            $procedure = ($_POST['Procedure'] == 'other') ? trim($_POST['Procedure_other']) : trim($item[$_POST['Procedure']]);
            $implantType = ($_POST['ImplantType'] == 'other') ? trim($_POST['ImplantType_other']) : trim($item[$_POST['ImplantType']]);
            $name = ($_POST['Name'] == 'other') ? trim($_POST['Name_other']) : trim($item[$_POST['Name']]);
            $details = ($_POST['Details'] == 'other') ? trim($_POST['Details_other']) : trim($item[$_POST['Details']]);
            $implantLine = ($_POST['ImplantLine'] == 'other') ? trim($_POST['ImplantLine_other']) : trim($item[$_POST['ImplantLine']]);
            $catalogNumber = ($_POST['CatalogNumber'] == 'other') ? trim($_POST['CatalogNumber_other']) : trim($item[$_POST['CatalogNumber']]);

            switch (CheckForRow($conn, $modelNumber, $barcode, $numbers)) {
                case "Insert":
                    if (!($insertStmt = $conn->prepare("INSERT INTO Device (ModelNumber, Barcode, Manufacturer, `Procedure`, ImplantType, `Name`, Details, ImplantLine, CatalogNumber, Added) " .
                        "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))
                    ) {
                        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                    }
                    if (!$insertStmt->bind_param("ssssssssss", $modelNumber, $barcode, $manufacturer, $procedure, $implantType, $name, $details, $implantLine, $catalogNumber, $added)) {
                        echo "Binding parameters failed: (" . $insertStmt->errno . ") " . $insertStmt->error;
                    }

                    $added = true;

                    if (!$insertStmt->execute()) {
                        echo "Execute failed: (" . $insertStmt->errno . ") " . $insertStmt->error;
                    } else {
                        echo "<span class='insert' id='$i'>.</span>";
                        $numbers->added++;
                        $row_num->added++;
                    }
                    $insertStmt->close();
                    break;
                case "Update":
                    if (!($updateStmt = $conn->prepare("UPDATE Device SET Barcode = ?, Updated = ? WHERE DeviceId = $numbers->id"))) {
                        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                    }
                    if (!$updateStmt->bind_param("ss", $barcode, $updated)) {
                        echo "Binding parameters failed: (" . $updateStmt->errno . ") " . $updateStmt->error;
                    }

                    $updated = true;

                    if (!$updateStmt->execute()) {
                        echo "Execute failed: (" . $updateStmt->errno . ") " . $updateStmt->error;
                    } else {
                        echo "<span class='update' id='$i'>.</span>";
                        $numbers->updated++;
                        $row_num->updated++;
                    }
                    $updateStmt->close();
                    break;
                case "Good":
                    echo "<span class='skipped' id='$i'>.</span>";
                    $numbers->skipped++;
                    $row_num->skipped++;
                    break;
            }
            if ($i % $width == 0) {
                echo "<small> - " . number_format($i) . " rows processed. (" . round(($i / $options->max) * 100, 2) . "%) <small><span class='number_s'>" . number_format($row_num->skipped) . " skipped</span>, " .
                    "<span class='number_u'>" . number_format($row_num->updated) . " updated</span>, <span class='number_i'>" . number_format($row_num->added) . " added</span>.</small></small><br>";
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
//        if ($i == 5) break;
    }

    echo "<small> - " . number_format($i) . " rows processed. (" . round(($i / $options->max) * 100, 2) . "%) <small><span class='number_s'>" . number_format($row_num->skipped) . " skipped</span>, " .
        "<span class='number_u'>" . number_format($row_num->updated) . " updated</span>, <span class='number_i'>" . number_format($row_num->added) . " added</span>.</small></small>";

    echo "<p>Complete! Out of " . number_format($options->max) . " rows, <span class='number_s'>" . number_format($numbers->skipped) . " were skipped</span>, " .
        "<span class='number_u'>" . number_format($numbers->updated) . " updated</span>, and <span class='number_i'>" . number_format($numbers->added) . " added</span>.<script>window.scrollTo(0,document.body.scrollHeight);</script>";

    $conn->close();
}

function CheckForRow($conn, $modelNumber, $barcode, $numbers) {
    $sql = "SELECT * FROM Device WHERE ModelNumber='$modelNumber'";
    $result = $conn->query($sql);
    $count = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["ModelNumber"] === $row["Barcode"] || str_replace("-", "", $row["ModelNumber"]) === $row["Barcode"]) {
                $numbers->id = $row["DeviceId"];
                return "Insert";
//                return "Update";
            } else {
                if ($row["Barcode"] == $barcode) {
                    $count++;
                }
            }
        }
    } else {
        return "Insert";
    }

    if ($count > 0) {
        return "Good";
    } else {
        return "Insert";
    }
}
?>