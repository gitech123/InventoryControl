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
    $sqlSelect = "SELECT * FROM barang_bekas_keluar
    WHERE 
    LPAD(barang_bekas_keluar.Bulan, 2, '0') BETWEEN LPAD('$startMonth', 2, '0') AND LPAD('$endMonth', 2, '0') 
    AND barang_bekas_keluar.Tahun BETWEEN '$startYear' AND '$endYear'
    AND Nama_Barang LIKE '%$item%'";
} else {
    $sqlSelect = "SELECT * FROM barang_bekas_keluar 
    WHERE 
    LPAD(barang_bekas_keluar.Bulan, 2, '0') BETWEEN LPAD('$startMonth', 2, '0') AND LPAD('$endMonth', 2, '0') 
    AND barang_bekas_keluar.Tahun BETWEEN '$startYear' AND '$endYear'";
}


// Pastikan $conn terdefinisi sebelumnya dan berhasil terhubung ke database
$result = $conn->query($sqlSelect);

// Membuat objek Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan header sesuai dengan format yang diinginkan
$sheet->setCellValue('A1', "Data Di Ambil Tanggal: $tanggalexport");
$sheet->setCellValue('A2', 'Tanggal Pengeluaran')
      ->setCellValue('B2', 'Kode Lokasi')
      ->setCellValue('C2', 'Kategori')
      ->setCellValue('D2', 'Nama Barang')
      ->setCellValue('E2', 'Jumlah')
      ->setCellValue('F2', 'Satuan')
      ->setCellValue('G2', 'No SPB')
      ->setCellValue('H2', 'Peruntukan')
      ->setCellValue('I2', 'Bulan')
      ->setCellValue('J2', 'Tahun');

// Menambahkan data dari database ke dalam file Excel
if ($result->num_rows > 0) {
    $rowIndex = 3; // Mulai dari baris ke-2 setelah header
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$rowIndex", $row['tanggal_pengeluaran']) // Pastikan nama kolom sesuai
              ->setCellValue("B$rowIndex", $row['kode_lokasi'])
              ->setCellValue("C$rowIndex", $row['kategori'])
              ->setCellValue("D$rowIndex", $row['nama_barang'])
              ->setCellValue("E$rowIndex", $row['jumlah'])
              ->setCellValue("F$rowIndex", $row['satuan'])
              ->setCellValue("G$rowIndex", $row['no_spb'])
              ->setCellValue("H$rowIndex", $row['peruntukan'])
              ->setCellValue("I$rowIndex", $row['bulan'])
              ->setCellValue("J$rowIndex", $row['tahun']);
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
    $fileName = "Data OUTBOUND $item Bekas ($tanggalexport).xlsx";
} else {
    $fileName = "Data OUTBOUND Bekas ($tanggalexport).xlsx";
}

// Mengirim file ke browser untuk diunduh
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>