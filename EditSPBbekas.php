<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ID = $_POST["id"];
    $jumlah = $_POST["jumlah"];
    $tgl = date("Y-m-d");
    $log = "#################################  Perubahan di Halaman Edit SPB Barang Bekas Masuk  #################################################\n"; // Variabel untuk mencatat log
    $log .= $Id . " ";
    $log .= "Diubah Oleh ";
    $log .= $user["username"];
    $log .= "\n";
    // Ambil data barang dari tabel `barang_bekas_masuk`
    $sqlBarang = "SELECT * FROM barang_bekas_masuk WHERE id = ?";
    $stmt = $conn->prepare($sqlBarang);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $barang = $result->fetch_assoc();

    $stockAwal = $barang["Jml"];
    $namabarang = $barang["Nama Barang"];
    echo "Nama Barang: $namabarang<br>";
    if ((int)$jumlah > (int)$stockAwal) {
        // Jika jumlah baru lebih besar dari stock awal
        $stockAkhir = (int)$jumlah - (int)$stockAwal;

        // Ambil data mutasi
        $sqlMutasi = "SELECT * FROM mutasi_part_bekas WHERE `Nama_Barang` COLLATE utf8_general_ci = ?";
        $stmt = $conn->prepare($sqlMutasi);
        $stmt->bind_param("s", $namabarang);
        $stmt->execute();
        $result = $stmt->get_result();
        $mutasi = $result->fetch_assoc();

        $saldoAkhir = $mutasi["Saldo_Akhir"] + (int)$stockAkhir; // Kurangi saldo
        $masuk = $mutasi["Masuk"] + (int)$stockAkhir; // Tambah barang masuk
        $log .= "[" . date("Y-m-d H:i:s") . "] Mutasi Barang Bekas Berubah UPDATE `mutasi_part_bekas` - Nama_Barang: $namabarang, Saldo_Akhir: {$mutasi["Saldo_Akhir"]} -> $saldoAkhir, Masuk: {$mutasi["Masuk"]} -> $masuk\n";


    } else {
        // Jika jumlah baru lebih kecil atau sama dengan stock awal
        $stockAkhir = (int)$stockAwal - (int)$jumlah;

        // Ambil data mutasi
        $sqlMutasi = "SELECT * FROM mutasi_part_bekas WHERE `Nama_Barang` COLLATE utf8_general_ci = ?";
        $stmt = $conn->prepare($sqlMutasi);
        $stmt->bind_param("s", $namabarang);
        $stmt->execute();
        $result = $stmt->get_result();
        $mutasi = $result->fetch_assoc();

        $saldoAkhir = $mutasi["Saldo_Akhir"] - (int)$stockAkhir; // Tambah saldo
        $masuk = $mutasi["Masuk"] - (int)$stockAkhir; // Kurangi barang masuk
        $log .= "[" . date("Y-m-d H:i:s") . "] Mutasi Barang Bekas Berubah UPDATE `mutasi_part_bekas` - Nama_Barang: $namabarang, Saldo_Akhir: {$mutasi["Saldo_Akhir"]} -> $saldoAkhir, Masuk: {$mutasi["Masuk"]} -> $masuk\n";

    }

    // Update tabel `mutasi_part_bekas`
    $sqlUpdatemutasi = "UPDATE mutasi_part_bekas SET Saldo_Akhir = ?, Masuk = ? WHERE `Nama_Barang` COLLATE utf8_general_ci = ?";
    $stmt = $conn->prepare($sqlUpdatemutasi);
    echo "Stock Awal: $stockAwal, Jumlah Baru: $jumlah, Stock Akhir: $stockAkhir<br>";
    echo "Saldo Akhir: $saldoAkhir, Masuk: $masuk, Nama Barang: $namabarang<br>";

    $stmt->bind_param("iis", $saldoAkhir, $masuk, $namabarang);
    if (!$stmt->execute()) {
        echo "Error pada query UPDATE mutasi_part_bekas: " . $conn->error;
        exit;
    }

    // Update tabel `barang_bekas_masuk`
    $sqlupdate = "UPDATE barang_bekas_masuk SET Jml = ? WHERE id = ?";
    $stmt = $conn->prepare($sqlupdate);
    echo "Stock Awal: $stockAwal, Jumlah Baru: $jumlah, Stock Akhir: $stockAkhir<br>";
    echo "Saldo Akhir: $saldoAkhir, Masuk: $masuk, Nama Barang: $namabarang<br>";
    $log .= "[" . date("Y-m-d H:i:s") . "] Jumlah Barang Masuk Berubah UPDATE `barang_bekas_masuk` - Nama_Barang: $namabarang, Jumlah: {$jumlah}\n";

    $stmt->bind_param("ii", $jumlah, $ID);
    if ($stmt->execute()) {
        file_put_contents("log_user.txt", $log, FILE_APPEND);
        echo "<script>alert('Jumlah barang berhasil diperbarui'); document.location = 'barang-bekas-masuk.php';</script>";
    } else {
        echo "Error pada query UPDATE barang_bekas_masuk: " . $conn->error;
    }
}
?>