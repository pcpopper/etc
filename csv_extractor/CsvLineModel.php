<?php


class CsvLineModel {

    public $ModelNumber;
    public $Name;
    public $Details;

    public $Barcode;
    public $CatalogNumber;

    public $Manufacturer;
    public $Procedure;
    public $ImplantType;
    public $ImplantLine;
    public $IsPublic;
    public $InstitutionId;

    public function __construct ($ModelNumber, $Name, $Details, // required
                                 $Barcode = null, $CatalogNumber = null, // optional
                                 $Manufacturer = 'undefined', $Procedure = 'undefined', $ImplantType = 'undefined',
                                 $ImplantLine = 'undefined', $IsPublic = 0, $InstitutionId = 10) {
        $this->ModelNumber = $ModelNumber;
        $this->Name = $Name;
        $this->Details = $Details;

        $this->Barcode = ($Barcode) ? $Barcode : $ModelNumber;
        $this->CatalogNumber = ($CatalogNumber) ? $CatalogNumber : $ModelNumber;

        $this->Manufacturer = $Manufacturer;
        $this->Procedure = $Procedure;
        $this->ImplantType = $ImplantType;
        $this->ImplantLine = $ImplantLine;
        $this->IsPublic = $IsPublic;
        $this->InstitutionId = $InstitutionId;
    }
}