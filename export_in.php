<?php
require 'vendor/autoload.php';
require "authMiddleware.php"; // file konfigurasi koneksi database
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['start_date']) || !isset($_GET['end_date'])) {
    die("Start date and end date are required");
}

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sparman_fix";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT
    barang_masuk.id,
    barang_masuk.jumlah, 
    barang_masuk.jumlah_awal, 
    barang_masuk.jumlah_akhir, 
    barang_masuk.tgl, 
    stock.area_penyimpanan,
    stock.kode_lokasi,
    stock.kategori,
    stock.tanggal,
    stock.po,
    stock.satuan,
    stock.supplier,
    stock.nama_barang,
    stock.peruntukan,
    barang_masuk.barang_id, 
    barang_masuk.user_id, 
    `user`.username
FROM
    barang_masuk
INNER JOIN
    stock ON barang_masuk.barang_id = stock.id
INNER JOIN
    `user` ON barang_masuk.user_id = `user`.id
WHERE
    barang_masuk.tgl BETWEEN '$startDate' AND '$endDate'
ORDER BY
    barang_masuk.id DESC";

$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header colors
$headerStyle = [
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'color' => ['argb' => 'FFFFFF00'], // Yellow color
    ],
    'font' => [
        'bold' => true,
    ],
];

// Set header values and styles
$sheet->setCellValue('A1', 'No')->getStyle('A1')->applyFromArray($headerStyle);
$sheet->setCellValue('B1', 'Area Penyimpanan')->getStyle('B1')->applyFromArray($headerStyle);
$sheet->setCellValue('C1', 'Kode Lokasi')->getStyle('C1')->applyFromArray($headerStyle);
$sheet->setCellValue('D1', 'Kategori')->getStyle('D1')->applyFromArray($headerStyle);
$sheet->setCellValue('E1', 'Tanggal')->getStyle('E1')->applyFromArray($headerStyle);
$sheet->setCellValue('F1', 'PO')->getStyle('F1')->applyFromArray($headerStyle);
$sheet->setCellValue('G1', 'Supplier')->getStyle('G1')->applyFromArray($headerStyle);
$sheet->setCellValue('H1', 'Nama Barang')->getStyle('H1')->applyFromArray($headerStyle);
$sheet->setCellValue('I1', 'Jml Awal')->getStyle('I1')->applyFromArray($headerStyle);
$sheet->setCellValue('J1', 'Jml Masuk')->getStyle('J1')->applyFromArray($headerStyle);
$sheet->setCellValue('K1', 'Jml Akhir')->getStyle('K1')->applyFromArray($headerStyle);
$sheet->setCellValue('L1', 'Satuan')->getStyle('L1')->applyFromArray($headerStyle);
$sheet->setCellValue('M1', 'Tanggal Masuk')->getStyle('M1')->applyFromArray($headerStyle);

$rowNumber = 2;
$no = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $no++);
        $sheet->setCellValue('B' . $rowNumber, $row['area_penyimpanan']);
        $sheet->setCellValue('C' . $rowNumber, $row['kode_lokasi']);
        $sheet->setCellValue('D' . $rowNumber, $row['kategori']);
        $sheet->setCellValue('E' . $rowNumber, $row['tgl']);
        $sheet->setCellValue('F' . $rowNumber, $row['po']);
        $sheet->setCellValue('G' . $rowNumber, $row['supplier']);
        $sheet->setCellValue('H' . $rowNumber, $row['nama_barang']);
        $sheet->setCellValue('I' . $rowNumber, $row['jumlah_awal']);
        $sheet->setCellValue('J' . $rowNumber, $row['jumlah']);
        $sheet->setCellValue('K' . $rowNumber, $row['jumlah_akhir']);
        $sheet->setCellValue('L' . $rowNumber, $row['satuan']);
        $sheet->setCellValue('M' . $rowNumber, $row['tgl']);
        $rowNumber++;
    }
}

// Auto resize columns
foreach (range('A', 'M') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$fileName = "History Masuk($startDate)/($endDate).xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>
