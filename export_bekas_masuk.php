<?php
require 'vendor/autoload.php';
require "authMiddleware.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Mendapatkan tanggal hari ini untuk nama file
$tanggalexport = date("d/m/Y");
$item = $_GET['item'];
$startDate = $_GET['start_date']; // Misalnya "2024-11-13"
$endDate = $_GET['end_date'];     // Misalnya "2024-12-15"

// Menggunakan objek DateTime
$startDateTime = new DateTime($startDate);
$endDateTime = new DateTime($endDate);

// Mendapatkan bulan dan tahun
$startMonth = $startDateTime->format("m"); // Output: "11"
$startYear = $startDateTime->format("Y");  // Output: "2024"
$endMonth = $endDateTime->format("m");     // Output: "12"
$endYear = $endDateTime->format("Y");      // Output: "2024"
// Query untuk mendapatkan data
if ($item != "") {
    $sqlSelect = "SELECT * FROM barang_bekas_masuk 
    WHERE 
    barang_bekas_masuk.Tanggal BETWEEN '$startDate' AND '$endDate'
    AND 'Nama Barang' LIKE '%$item%'";
} else {
    $sqlSelect = "SELECT * FROM barang_bekas_masuk 
    WHERE 
    barang_bekas_masuk.Tanggal BETWEEN '$startDate' AND '$endDate'";
}


// Pastikan $conn terdefinisi sebelumnya dan berhasil terhubung ke database
$result = $conn->query($sqlSelect);

// Membuat objek Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header sesuai dengan format yang diinginkan
$sheet->setCellValue('A1', "Data Di Ambil Tanggal: $tanggalexport");
$sheet->setCellValue('A2', 'Area Penyimpanan')
      ->setCellValue('B2', 'Kode Lokasi')
      ->setCellValue('C2', 'Tanggal Penerimaan')
      ->setCellValue('D2', 'Kategori')
      ->setCellValue('E2', 'Nama Barang')
      ->setCellValue('F2', 'Jml')
      ->setCellValue('G2', 'Uom')
      ->setCellValue('H2', 'No SPB')
      ->setCellValue('I2', 'Bulan')
      ->setCellValue('J2', 'Tahun');

// Menambahkan data dari database ke dalam file Excel
if ($result->num_rows > 0) {
    $rowIndex = 3; // Mulai dari baris ke-2 setelah header
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$rowIndex", $row['Area Penyimpanan']) // Pastikan nama kolom sesuai
              ->setCellValue("B$rowIndex", $row['Kode Lokasi'])
              ->setCellValue("C$rowIndex", $row['Tanggal'])
              ->setCellValue("D$rowIndex", $row['Kategori'])
              ->setCellValue("E$rowIndex", $row['Nama Barang'])
              ->setCellValue("F$rowIndex", $row['Jml'])
              ->setCellValue("G$rowIndex", $row['Uom'])
              ->setCellValue("H$rowIndex", $row['No SPB'])
              ->setCellValue("I$rowIndex", $row['Bulan'])
              ->setCellValue("J$rowIndex", $row['Tahun']);
        $rowIndex++;
    }
}
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
$spreadsheet->getActiveSheet()->getStyle('A2:J2')->applyFromArray($headerStyle);
foreach(range('A','J') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}
// Membuat nama file
if ($item != "") {
    $fileName = "Data INBOUND $item Bekas ($tanggalexport).xlsx";
} else {
    $fileName = "Data INBOUND Bekas ($tanggalexport).xlsx";
}

// Mengirim file ke browser untuk diunduh
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>