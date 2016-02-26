<?php

require_once('Extractor.php');

class smr_product_catalog Extends Extractor {

    public function __construct($file) {
        parent::__construct($file);
    }

    public function extractInfo ($csv) {
        $out = array();
        $groupHeader = "";
        $sectionHeader = "";
        foreach ($csv as $idx => $line) {
            if ($line[0] == "" && $line[1] != "") {
                if ($csv[$idx+1][0] == "COD.") {
                    $groupHeader = $line[1];
                } else {
                    $sectionHeader = $line[1];
                }
            } else if ($line[0] != "" && $line[0] != "COD.") {
                if ($sectionHeader == "Instrument Set") {
                    $name = $groupHeader . ", Instrument Set";
                    $details = $groupHeader . ", " . $line[1];
                } else {
                    $name = $sectionHeader;
                    $details = $sectionHeader . ", " . $line[1];
                }
                $out[] = new CsvLineModel(str_replace(".", "", $line[0]), $name, $details);
            }
        }

        return $out;
    }

}