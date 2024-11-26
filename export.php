<?php
require 'vendor/autoload.php';
require "authMiddleware.php"; // file konfigurasi koneksi database
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['start_date']) || !isset($_GET['end_date'])) {
}

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$item = $_GET['item'];
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
if ($item != ""){
$sql = "SELECT
    ambil_barang.id, 
    ambil_barang.teknisi_id, 
    ambil_barang.`no`, 
    ambil_barang.tgl_permintaan, 
    ambil_barang.keperluan, 
    ambil_barang.tgl_dibutuhkan, 
    ambil_barang.dikirim_ke, 
    ambil_barang.`status`, 
    ambil_barang.`tgl_validasi`, 
    ambil_barang.no_spb, 
    ambil_barang.bulan_spb, 
    ambil_barang.tahun_spb, 
    stock.nama_barang, 
    stock.kode_lokasi,
    stock.kategori,
    ambil_barang_item.satuan, 
    ambil_barang_item.jumlah
FROM
    ambil_barang
INNER JOIN
    ambil_barang_item
ON 
    ambil_barang.id = ambil_barang_item.ambil_barang_id
INNER JOIN
    stock
ON 
    ambil_barang_item.barang_id = stock.id
WHERE
    ambil_barang.tgl_permintaan BETWEEN '$startDate' AND '$endDate'
    AND stock.nama_barang LIKE '%$item%' -- filter item berdasarkan nama_barang
ORDER BY
    ambil_barang.id DESC";
}
else 
{
    $sql = "SELECT
ambil_barang.id, 
ambil_barang.teknisi_id, 
ambil_barang.no, 
ambil_barang.tgl_permintaan, 
ambil_barang.keperluan, 
ambil_barang.tgl_dibutuhkan, 
ambil_barang.dikirim_ke, 
ambil_barang.status, 
ambil_barang.tgl_validasi, 
ambil_barang.no_spb, 
ambil_barang.bulan_spb, 
ambil_barang.tahun_spb, 
stock.nama_barang, 
stock.kode_lokasi,
stock.kategori,
ambil_barang_item.satuan, 
ambil_barang_item.jumlah
FROM
ambil_barang
INNER JOIN
ambil_barang_item
ON 
ambil_barang.id = ambil_barang_item.ambil_barang_id
INNER JOIN
stock
ON 
ambil_barang_item.barang_id = stock.id
WHERE
ambil_barang.tgl_permintaan BETWEEN '$startDate' AND '$endDate'
ORDER BY
ambil_barang.id DESC";
}

$stmt = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Tanggal Pengeluaran');
$sheet->setCellValue('C1', 'Kode Lokasi');
$sheet->setCellValue('D1', 'Nama Barang');
$sheet->setCellValue('E1', 'Jumlah');
$sheet->setCellValue('F1', 'Satuan');
$sheet->setCellValue('G1', 'No SPB');
$sheet->setCellValue('H1', 'Peruntukan');
$sheet->setCellValue('I1', 'Nama Pengambil');

$rowNumber = 3;
if ($stmt->num_rows > 0):
$no = 1;
 while ($row = $stmt->fetch_assoc()):
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['tgl_permintaan']);
    $sheet->setCellValue('C' . $rowNumber, $row['kode_lokasi']);
    $sheet->setCellValue('D' . $rowNumber, $row['nama_barang']);
    $sheet->setCellValue('E' . $rowNumber, $row['jumlah']);
    $sheet->setCellValue('F' . $rowNumber, $row['satuan']);
    $sheet->setCellValue('G' . $rowNumber, ($row['kategori'] == 'Non Stock') ? 'GSP/'.$row['no_spb'].'/'.$row['bulan_spb'].'/'.$row['tahun_spb'].'/NS' : $row['no_spb'].'/'.'BPB/3S1/'.$row['bulan_spb'].'/'.$row['tahun_spb']);
    $sheet->setCellValue('H' . $rowNumber, $row['keperluan']);
    $sheet->setCellValue('I' . $rowNumber, $row['teknisi_id']);
    $rowNumber++;       
endwhile;
endif;
$yellowColor = 'FFFFFF00';
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => $yellowColor], // Warna kuning
    ],
];
$spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($headerStyle);
foreach(range('A','I') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}
$writer = new Xlsx($spreadsheet);
$fileName = "History Keluar($startDate)/($endDate).xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>