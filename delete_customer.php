<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['customer'] ?? '';
    $filename = 'vrf_models.xlsx';

    if (empty($customerName)) {
        echo json_encode(['success' => false, 'message' => 'Customer name is required.']);
        exit;
    }

    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    try {
        $spreadsheet = IOFactory::load($filename);
        $sheetIndex = $spreadsheet->getIndex(
            $spreadsheet->getSheetByName($customerName)
        );
        $spreadsheet->removeSheetByIndex($sheetIndex);

        // Save updated file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        echo json_encode(['success' => true, 'message' => 'Customer deleted successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>