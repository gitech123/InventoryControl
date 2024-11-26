<?php
require "authMiddleware.php";

if (isset($_GET["id"])) {
    $id  = $_GET["id"];
    $sqlBarang   = "SELECT * FROM barang_masuk WHERE id = $id";
    $queryBarang = $conn->query($sqlBarang);
    $barang      = $queryBarang->fetch_assoc();
    $stockreturn   = $barang["jumlah"];
    $barangid   = $barang["barang_id"];

    $sqlBarangstock   = "SELECT * FROM stock WHERE id = $barangid";
    $queryBarangstock = $conn->query($sqlBarangstock);
    $barangstock      = $queryBarangstock->fetch_assoc();
    $stock   = $barangstock["jumlah"];
    $stockAkhir  = $barangstock["jumlah"] - (int)$stockreturn;

    $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhir WHERE id = $barangid";

        if ($conn->query($sqlUpdateStock)) {
            $sql = "DELETE FROM barang_masuk where id = $id";
    if ($conn->query($sql)) {
        echo "<script> alert('hapus History berhasil');document.location = 'historyStock.php'; </script>";
    }
        }  
}
?>