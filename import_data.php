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
<?php
$csv = array_map('str_getcsv', file('DePuy Orthopaedics GTIN Cross Reference List/Sheet1-Table 1.csv'));

$servername = "databases.oberd.dev";
$username = "root";
$password = "rawr";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$count = count($csv) - 1;
$numbers = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0, 'id'=>0);
$row_num = (object) array('skipped'=>0, 'updated'=>0, 'added'=>0);
$width = 250;

$i = 0;
foreach ($csv as $item) {
    if ($i) {
        switch (CheckForRow($conn, $item[0], $item[4], $numbers)) {
            case "Insert":
                if (!($insertStmt = $conn->prepare("INSERT INTO medadatonline_demo.Device (ModelNumber, Barcode, Manufacturer, `Procedure`, ImplantType, `Name`, Details, ImplantLine, CatalogNumber, Added) " .
                    "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))) {
                    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                }
                if (!$insertStmt->bind_param("ssssssssss", $modelNumber, $barcode, $manufacturer, $procedure, $implant, $name, $name, $implant, $modelNumber, $added)) {
                    echo "Binding parameters failed: (" . $insertStmt->errno . ") " . $insertStmt->error;
                }

                $modelNumber = $item[0];
                $barcode = $item[4];
                $manufacturer = $item[5];
                $procedure = "TKA";
                $implant = "undefined";
                $name = $item[1];
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
                if (!($updateStmt = $conn->prepare("UPDATE medadatonline_demo.Device SET Barcode = ?, Updated = ? WHERE DeviceId = $numbers->id"))) {
                    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                }
                if (!$updateStmt->bind_param("ss", $barcode, $updated)) {
                    echo "Binding parameters failed: (" . $updateStmt->errno . ") " . $updateStmt->error;
                }

                $barcode = $item[4];
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
//    if ($i == 5) break;
}


function CheckForRow($conn, $modelNumber, $barcode, $numbers) {
    $sql = "SELECT * FROM medadatonline_demo.Device WHERE ModelNumber='$modelNumber'";
    $result = $conn->query($sql);
    $count = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["ModelNumber"] === $row["Barcode"] || str_replace("-", "", $row["ModelNumber"]) === $row["Barcode"]) {
                $numbers->id = $row["DeviceId"];
                return "Update";
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
echo "<small> - ".number_format($i)." rows processed. (" . round(($i / $options->max) * 100, 2) . "%) <small><span class='number_s'>".number_format($row_num->skipped)." skipped</span>, " .
    "<span class='number_u'>".number_format($row_num->updated)." updated</span>, <span class='number_i'>".number_format($row_num->added)." added</span>.</small></small>";

echo "<p>Complete! Out of ".number_format($options->max)." rows, <span class='number_s'>".number_format($numbers->skipped)." were skipped</span>, " .
    "<span class='number_u'>".number_format($numbers->updated)." updated</span>, and <span class='number_i'>".number_format($numbers->added)." added</span>.<script>window.scrollTo(0,document.body.scrollHeight);</script>";

$conn->close();
?>