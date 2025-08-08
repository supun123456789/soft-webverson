<?php
require 'autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

$query = strtolower(trim($_GET['query'] ?? ''));
if ($query === '') {
    echo json_encode(['success' => false, 'message' => 'Query is empty.']);
    exit;
}

$file = 'vrf_models.xlsx';
if (!file_exists($file)) {
    echo json_encode(['success' => false, 'message' => 'Data file not found.']);
    exit;
}

try {
    $spreadsheet = IOFactory::load($file);
    $results = [];
    $total = 0;

    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        $data = $sheet->toArray(null, true, true, true);

        foreach ($data as $i => $row) {
            if ($i === 1) continue; // Skip header

            $outModel = strtolower($row['A'] ?? '');
            $outQty = (int)($row['B'] ?? 0);
            $inModel = strtolower($row['D'] ?? '');
            $inQty = (int)($row['E'] ?? 0);

            if ($outModel && strpos($outModel, $query) !== false) {
                $results[] = [
                    'customer' => $sheetName,
                    'type' => 'Outdoor Model',
                    'model' => $row['A'],
                    'qty' => $outQty
                ];
                $total += $outQty;
            }

            if ($inModel && strpos($inModel, $query) !== false) {
                $results[] = [
                    'customer' => $sheetName,
                    'type' => 'Indoor Model',
                    'model' => $row['D'],
                    'qty' => $inQty
                ];
                $total += $inQty;
            }
        }
    }

    if (count($results) === 0) {
        echo json_encode(['success' => false, 'message' => 'No models found.']);
    } else {
        echo json_encode(['success' => true, 'results' => $results, 'total' => $total]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
