<?php

require_once('Extractor.php');

class lima_catalog Extends Extractor {

    public function __construct($file) {
        parent::__construct($file);
    }

    public function extractInfo ($csv) {
        $sectionHeader = true;
        $sectionTemplate = null;

        $out = array();
        foreach ($csv as $line) {
            if ($line[0] == "") {
                $sectionHeader = true;
                continue;
            }

            if ($sectionHeader) {
                $sectionTemplate = $this->buildSectionTemplate($line);
                $sectionHeader = false;
            } else {
                $out[] = new CsvLineModel(str_replace(".", "", $line[1]), $line[0], $this->buildRowDetails($line, $sectionTemplate));
            }
        }

        return $out;
    }

    private function buildSectionTemplate ($line) {
        $out = array();
        $exclusions = array('Size');

        for ($i = 2; $i < count($line); $i++) {
            if ($line[$i] != "") {
                if (in_array(trim($line[$i]), $exclusions)) {
                    $out[$i] = '';
                } else {
                    $out[$i] = (object) array(
                        'pre' => (strpos($line[$i], "mm") !== false) ? trim(str_replace("(mm)", "", $line[$i])) : trim($line[$i]),
                        'post' => (strpos($line[$i], "mm") !== false) ? "mm" : "",
                    );
                }
            }
        }

        return $out;
    }

    private function buildRowDetails ($line, $sectionTemplate) {
        $out = array();
        $out[] = $line[0];

        foreach ($sectionTemplate as $k => $v) {
            if (is_object($v)) {
                $out[] = $v->pre . ": " . $line[$k] . $v->post;
            } else {
                if ($line[$k] != "") {
                    $out[] = $line[$k];
                }
            }
        }

        return join(", ", $out);
    }

}