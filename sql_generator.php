<form method="get">
	<input type="radio" id="sql-type" name="sql-type" value="update">Update
	<input type="radio" id="sql-type" name="sql-type" value="insert">Insert
	<input type="submit" value="Start">
</form>

<?php
if (isset($_GET['sql-type'])) {
	$csv = array_map('str_getcsv', file('csv.csv'));
	$i = 0;

	$sqlInsert = "INSERT INTO `medadatonline_demo`.`Device` (";
	foreach ($csv[0] as $col) {
		$sqlInsert .= "`" . $col . "`,";
	}
	$sqlInsert = substr($sqlInsert, 0, -1) . ") VALUES (";

	foreach ($csv as $row) {
		if ($i) {
			$j = 0;

			if ($_GET['sql-type'] == "update") {
				$sql = "UPDATE `medadatonline_demo`.`Device` SET ";

				foreach ($row as $col) {
					$sql .= "`" . $cols[$j] . "` = ";
					$sql .= (is_numeric($col)) ? $col . ", " : "'" . $col . "', ";
					$j++;
				}

				$sql = substr($sql, 0, -2) . " WHERE `" . $cols[0] . "` = " . $row[0] . ";";
			} else {
				$sql = $sqlInsert;
				foreach ($row as $col) {
					$sql .= (is_numeric($col)) ? $col . ", " : "'" . $col . "', ";
					$j++;
				}

				$sql = substr($sql, 0, -2) . ");";
			}

			echo $sql;
		} else {
			$cols = $row;
		}

		echo "<p>";
		$i++;
//		if ($i == 5) break;
	}
}
?>