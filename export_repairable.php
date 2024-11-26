<?php
require 'vendor/autoload.php';
require "authMiddleware.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Mendapatkan tanggal hari ini untuk nama file
$tanggalexport = date("d/m/Y");
$item = $_GET['item'];
$tanggalexport = date("d/m/Y");
// Query untuk mendapatkan data
if ($item != "") {
    $sqlSelect = "SELECT * FROM part_repairable WHERE Nama_Barang LIKE '%$item%'";
} else {
    $sqlSelect = "SELECT * FROM part_repairable";
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
      ->setCellValue('C2', 'Kategori')
      ->setCellValue('D2', 'Nama Barang')
      ->setCellValue('E2', 'Satuan')
      ->setCellValue('F2', 'Saldo Awal')
      ->setCellValue('G2', 'Masuk')
      ->setCellValue('H2', 'Keluar')
      ->setCellValue('I2', 'Saldo Akhir')
      ->setCellValue('J2', 'Keterangan Pengajuan Disposisi')
      ->setCellValue('K2', 'Keterangan Potensi Pemakaian Barang');

// Menambahkan data dari database ke dalam file Excel
if ($result->num_rows > 0) {
    $rowIndex = 3; // Mulai dari baris ke-2 setelah header
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$rowIndex", $row['Area_Penyimpanan']) // Pastikan nama kolom sesuai
              ->setCellValue("B$rowIndex", $row['Kode_lokasi'])
              ->setCellValue("C$rowIndex", $row['Kategori'])
              ->setCellValue("D$rowIndex", $row['Nama_Barang'])
              ->setCellValue("E$rowIndex", $row['Satuan'])
              ->setCellValue("F$rowIndex", $row['Saldo_Awal'])
              ->setCellValue("G$rowIndex", $row['Masuk'])
              ->setCellValue("H$rowIndex", $row['Keluar'])
              ->setCellValue("I$rowIndex", $row['Saldo_Akhir'])
              ->setCellValue("J$rowIndex", $row['Keterangan_Pengajuan_Disposisi'])
              ->setCellValue("K$rowIndex", $row['Keterangan_Potensi_Pemakaian_Barang']);
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
$spreadsheet->getActiveSheet()->getStyle('A2:K2')->applyFromArray($headerStyle);
foreach(range('A','K') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}
// Membuat nama file
if ($item != "") {
    $fileName = "Data $item Repairable ($tanggalexport).xlsx";
} else {
    $fileName = "Data Barang Repairable ($tanggalexport).xlsx";
}

// Mengirim file ke browser untuk diunduh
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>