<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ambilbarangId = $_POST["id"];
    $barangId = $_POST["barang_id"];
    $jumlah   = $_POST["jumlah"];
    $tgl      = date("Y-m-d");

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
    }
        if ($conn->query($sqlUpdateStock)) {
            echo "<script> alert('tambah jumlah barang berhasil');document.location = 'daftarBarang.php' </script>";
        }
    }

?>