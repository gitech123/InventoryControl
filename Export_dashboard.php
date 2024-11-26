<?php
require 'vendor/autoload.php';
require "authMiddleware.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Mendapatkan tanggal hari ini untuk nama file
$tanggalexport = date("d/m/Y");
$kategori = $_GET['kategori'];
// Query untuk mendapatkan data
if ($kategori != "") {
    $sqlSelect = "SELECT * FROM stock WHERE kategori = '$kategori' ";
} else {
    $sqlSelect = "SELECT * FROM stock";
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
      ->setCellValue('I2', 'Saldo Akhir');

// Menambahkan data dari database ke dalam file Excel
if ($result->num_rows > 0) {
    $rowIndex = 3; // Mulai dari baris ke-2 setelah header
    while ($row = $result->fetch_assoc()) {

        $barangId = $row['id'];
        $jumlah = $row['jumlah'];
        $safetyStock = $row['safety_stock'];
        $jmlsaldoawal = 0;
        $sqlBarangMasuk = "SELECT * FROM barang_masuk where barang_id = $barangId";
        $resultBarangMasuk = $conn->query($sqlBarangMasuk);
        $jmlBarangMasuk = 0;
        while ($barangMasuk = $resultBarangMasuk->fetch_assoc()) {
            $jmlBarangMasuk += $barangMasuk["jumlah"];
        }
        $sqlBarangKeluar = "SELECT * FROM ambil_barang_item where barang_id = $barangId";
        $resultBarangKeluar = $conn->query($sqlBarangKeluar);
        $jmlBarangKeluar = 0;
        while ($barangKeluar = $resultBarangKeluar->fetch_assoc()) {
            $jmlBarangKeluar += $barangKeluar["jumlah"];
        }
        if($jmlBarangKeluar > 0 && $jmlBarangMasuk < 1) {
            $jmlsaldoawal = $row['jumlah'] + $jmlBarangKeluar;
        } else if($jmlBarangKeluar > 0 && $jmlBarangMasuk > 0) {
            $jmlsaldoawal = $row['jumlah'] + ($jmlBarangKeluar - $jmlBarangMasuk);
        } else if($jmlBarangKeluar < 1 && $jmlBarangMasuk > 0) {
            $jmlsaldoawal = $row['jumlah'] - $jmlBarangMasuk;
        } else {
            $jmlsaldoawal = $row['jumlah'];
        }

        $sheet->setCellValue("A$rowIndex", $row['area_penyimpanan']) // Pastikan nama kolom sesuai
              ->setCellValue("B$rowIndex", $row['kode_lokasi'])
              ->setCellValue("C$rowIndex", $row['kategori'])
              ->setCellValue("D$rowIndex", $row['nama_barang'])
              ->setCellValue("E$rowIndex", $row['satuan'])
              ->setCellValue("F$rowIndex", $jmlsaldoawal)
              ->setCellValue("G$rowIndex", $jmlBarangMasuk)
              ->setCellValue("H$rowIndex", $jmlBarangKeluar)
              ->setCellValue("I$rowIndex", $row['jumlah']);
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
$spreadsheet->getActiveSheet()->getStyle('A2:I2')->applyFromArray($headerStyle);
foreach(range('A','I') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}
// Membuat nama file
if ($kategori != "") {
    $fileName = "Data $kategori ($tanggalexport).xlsx";
} else {
    $fileName = "Data Dashboard ($tanggalexport).xlsx";
}

// Mengirim file ke browser untuk diunduh
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>