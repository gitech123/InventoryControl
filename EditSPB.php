<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ambilbarangId = $_POST["id"];
    $barangId = $_POST["barang_id"];
    $jumlah   = $_POST["jumlah"];
    $tgl      = date("Y-m-d");
    $log = "#################################  Perubahan di Halaman Edit SPB Barang Keluar  #################################################\n"; // Variabel untuk mencatat log
    $log .= "Diubah Oleh ";
    $log .= $user["username"];
    $log .= "\n";
    $sqlBarang   = "SELECT * FROM ambil_barang_item WHERE ambil_barang_id = $ambilbarangId AND barang_id = $barangId";
    $queryBarang = $conn->query($sqlBarang);
    $barang      = $queryBarang->fetch_assoc();
    $stockAwal   = $barang["jumlah"];
    //$barangId    = $barang["barang_id"];
    if((int)$jumlah > $stockAwal){
        $stockAkhir = (int)$jumlah - (int)$stockAwal;
        $sqlBarang   = "SELECT * FROM stock WHERE id = $barangId";
        $queryBarang = $conn->query($sqlBarang);
        $barang      = $queryBarang->fetch_assoc();
        $stockAkhirst  = $barang["jumlah"] - (int)$stockAkhir;
        $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhirst WHERE id = $barangId";
        $sqlupdateambil = "UPDATE ambil_barang_item SET jumlah = $jumlah WHERE ambil_barang_id = $ambilbarangId AND barang_id = $barangId";
        $result = $conn->query($sqlupdateambil);
        $log .= "[" . date("Y-m-d H:i:s") . "] EDIT SPB BARANG MASUK UPDATE `stock` - Nama_Barang: $barang[nama_barang], Saldo_Akhir: {$stockAkhirst} -> \n";
        $log .= "[" . date("Y-m-d H:i:s") . "] EDIT SPB BARANG MASUK UPDATE `ambil_barang_item` - Nama_Barang: $barang[nama_barang], Jumlah: {$jumlah} -> \n";
    }
    else{
        $stockAkhir = (int)$stockAwal - (int)$jumlah;
        $sqlBarang   = "SELECT * FROM stock WHERE id = $barangId";
        $queryBarang = $conn->query($sqlBarang);
        $barang      = $queryBarang->fetch_assoc();
        $stockAkhirst  = $barang["jumlah"] + (int)$stockAkhir;
        $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhirst WHERE id = $barangId";
        $sqlupdateambil = "UPDATE ambil_barang_item SET jumlah = $jumlah WHERE ambil_barang_id = $ambilbarangId AND barang_id = $barangId";
        $result = $conn->query($sqlupdateambil);
        $log .= "[" . date("Y-m-d H:i:s") . "] EDIT SPB BARANG MASUK UPDATE `stock` - Nama_Barang: $barang[nama_barang], Saldo_Akhir: {$stockAkhirst} -> \n";
        $log .= "[" . date("Y-m-d H:i:s") . "] EDIT SPB BARANG MASUK UPDATE `ambil_barang_item` - Nama_Barang: $barang[nama_barang], Jumlah: {$jumlah} -> \n";
    }
        if ($conn->query($sqlUpdateStock)) {
            file_put_contents("log_user.txt", $log, FILE_APPEND);
            echo "<script> alert('tambah jumlah barang berhasil');document.location = 'daftarBarang.php' </script>";
        }
    }

?>