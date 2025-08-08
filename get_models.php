<?php
$filename = 'vrf_models.xlsx';
require 'autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load($filename);
$models = [];

foreach ($spreadsheet->getSheetNames() as $sheetName) {
  $sheet = $spreadsheet->getSheetByName($sheetName);
  $data = $sheet->toArray();
  foreach ($data as $row) {
    if (!empty($row[0])) $models[] = $row[0]; // Outdoor
    if (!empty($row[3])) $models[] = $row[3]; // Indoor
  }
}

$models = array_unique(array_filter($models));
sort($models);

header('Content-Type: application/json');
echo json_encode($models);
?>