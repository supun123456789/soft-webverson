<?php
$filename = 'vrf_models.xlsx';

require 'autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load($filename);
$sheetNames = $spreadsheet->getSheetNames();

header('Content-Type: application/json');
echo json_encode($sheetNames);
?>