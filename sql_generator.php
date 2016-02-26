<form method="get">
	<input type="radio" id="sql-type" name="sql-type" value="update">Update
	<input type="radio" id="sql-type" name="sql-type" value="insert">Insert
	<input type="file" id="filename" name="filename">
	<input type="submit" value="Start">
</form>

<?php
if (isset($_GET['sql-type'])) {
	$file = (isset($_GET['filename'])) ? trim($_GET['filename']) : 'csv.csv';
	$csv = array_map('str_getcsv', file($file));
	$i = 0;

	$sqlInsert = "INSERT INTO `Device` (";
	foreach ($csv[0] as $col) {
		$sqlInsert .= "`" . $col . "`,";
	}
	$sqlInsert = substr($sqlInsert, 0, -1) . ")\nVALUES\n\t (";

	$k = 1;
	foreach ($csv as $row) {
		if ($i) {
			$j = 0;

			if ($_GET['sql-type'] == "update") {
				$sql = "UPDATE `Device` SET ";

				$sets = array();
				foreach ($row as $col) {
					if ($j == 0) {
						$j++;
						continue;
					}
					$str = "`" . $cols[$j] . "` = ";
					$str .= (is_numeric($col)) ? $col : "'" . $col . "'";
					$sets[] = $str;
					$j++;
				}

				$sql = $sql . join(", ", $sets) . " WHERE `" . $cols[0] . "` = " . $row[0] . ";\n";
				echo $sql;
			} else {
				$sql = ($k == 1) ? $sqlInsert : "\t(";
				foreach ($row as $col) {
					$sql .= (is_numeric($col)) ? $col . ", " : "'" . str_replace("'", "''", trim($col)) . "', ";
					$j++;
				}

				if ($k == 1500) {
					$sql = substr($sql, 0, -2) . ");\n\n";
					$k = 1;
				} else {
					$sql = substr($sql, 0, -2) . "),\n";
					$k++;
				}
				echo $sql;
			}
} else {
			$cols = $row;
		}

		$i++;
//		if ($i == 5) break;
	}
}
?>