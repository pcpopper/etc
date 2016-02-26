<?php

require_once('CsvLineModel.php');


abstract class Extractor {

    public function __construct($file) {
        $csv = $this->readCsv($file);
        $extracted = $this->extractInfo($csv);
        $this->writeCsv($extracted, $file);
    }

    abstract function extractInfo ($csv);

    private function readCsv ($file) {
        return array_map('str_getcsv', file($file . '.csv'));
    }

    private function writeCsv ($extracted, $file) {
        $headers = array_keys((array) $extracted[0]);

        $fp = fopen($file . '_output.csv', 'w');

        fputcsv($fp, $headers);

        foreach ($extracted as $line) {
            fputcsv($fp, (array) $line);
        }

        fclose($fp);
    }

}