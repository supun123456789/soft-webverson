<?php
require 'autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

$query = strtolower(trim($_GET['query'] ?? ''));
if ($query === '') {
    echo json_encode(['success' => false, 'message' => 'Query is empty.']);
    exit;
}

$partsFile = 'parts.xlsx';
$vrfFile = 'vrf_models.xlsx';

if (!file_exists($partsFile) || !file_exists($vrfFile)) {
    echo json_encode(['success' => false, 'message' => 'One or both data files are missing.']);
    exit;
}

try {
    $partsSpreadsheet = IOFactory::load($partsFile);
    $vrfSpreadsheet = IOFactory::load($vrfFile);

    $matchedModels = [];

    // 1. Search parts.xlsx for ReferenceNo matches
    foreach ($partsSpreadsheet->getSheetNames() as $sheetName) {
        $sheet = $partsSpreadsheet->getSheetByName($sheetName);
        $data = $sheet->toArray(null, true, true, true);

        foreach ($data as $i => $row) {
            if ($i === 1) continue;
            $ref = strtolower($row['A'] ?? '');
            if ($ref && strpos($ref, $query) !== false) {
                $matchedModels[$sheetName] = [
                    'qty' => (int)($row['B'] ?? 0),
                    'desc' => $row['C'] ?? 'N/A'
                ];
                break;
            }
        }
    }

    if (empty($matchedModels)) {
        echo json_encode(['success' => false, 'message' => 'Part number not found.']);
        exit;
    }

    $totalNeeded = 0;
    $matches = [];

    // 2. Count matching models from vrf_models.xlsx
    foreach ($vrfSpreadsheet->getSheetNames() as $sheetName) {
        $sheet = $vrfSpreadsheet->getSheetByName($sheetName);
        $data = $sheet->toArray(null, true, true, true);

        foreach ($data as $i => $row) {
            if ($i === 1) continue;
            $outModel = strtolower($row['A'] ?? '');
            $outQty = (int)($row['B'] ?? 0);
            $inModel = strtolower($row['D'] ?? '');
            $inQty = (int)($row['E'] ?? 0);

            foreach ($matchedModels as $model => $info) {
                if ($outModel === strtolower($model)) {
                    $totalNeeded += $info['qty'] * $outQty;
                    $matches[] = [
                        'model' => $model,
                        'qty' => $info['qty'],
                        'desc' => $info['desc']
                    ];
                }
                if ($inModel === strtolower($model)) {
                    $totalNeeded += $info['qty'] * $inQty;
                    $matches[] = [
                        'model' => $model,
                        'qty' => $info['qty'],
                        'desc' => $info['desc']
                    ];
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'matches' => $matches,
        'total' => $totalNeeded
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
