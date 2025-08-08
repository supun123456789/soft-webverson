<?php
require 'autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$customerName = trim($_POST['customerName'] ?? '');
$outdoorModels = $_POST['outdoor_model'] ?? [];
$outdoorQty = $_POST['outdoor_qty'] ?? [];
$outdoorSerial = $_POST['outdoor_serial'] ?? [];
$indoorModels = $_POST['indoor_model'] ?? [];
$indoorQty = $_POST['indoor_qty'] ?? [];
$indoorSerial = $_POST['indoor_serial'] ?? [];

if ($customerName === '') {
    echo json_encode(['success' => false, 'message' => 'Customer name is required.']);
    exit;
}

$file = 'vrf_models.xlsx';

try {
    if (file_exists($file)) {
        $spreadsheet = IOFactory::load($file);
    } else {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // remove default sheet
    }

    if ($spreadsheet->sheetNameExists($customerName)) {
        $spreadsheet->removeSheetByIndex($spreadsheet->getIndex(
            $spreadsheet->getSheetByName($customerName)
        ));
    }

    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($customerName);
    $sheet->fromArray([
        'Outdoor Model', 'Outdoor Quantity', 'Outdoor Serial(s)',
        'Indoor Model', 'Indoor Quantity', 'Indoor Serial(s)'
    ], null, 'A1');

    $row = 2;
    for ($i = 0; $i < count($outdoorModels); $i++) {
        $sheet->setCellValue("A{$row}", $outdoorModels[$i]);
        $sheet->setCellValue("B{$row}", $outdoorQty[$i]);
        $sheet->setCellValue("C{$row}", $outdoorSerial[$i]);
        $row++;
    }
    for ($j = 0; $j < count($indoorModels); $j++) {
        $sheet->setCellValue("D{$row}", $indoorModels[$j]);
        $sheet->setCellValue("E{$row}", $indoorQty[$j]);
        $sheet->setCellValue("F{$row}", $indoorSerial[$j]);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($file);

    echo json_encode(['success' => true, 'message' => 'Customer data saved.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
