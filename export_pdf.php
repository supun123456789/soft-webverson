<?php
require_once __DIR__ . 'autoload.php';
use Dompdf\Dompdf;

$data = $_POST['data'] ?? '';

$dompdf = new Dompdf();
$dompdf->loadHtml('<pre>' . htmlspecialchars($data) . '</pre>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("report.pdf", ["Attachment" => true]);
?>