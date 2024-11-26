<?php
require "authMiddleware.php";

if (isset($_GET["id"])) {
    $id  = $_GET["id"];
    $sqlBarang   = "SELECT * FROM ambil_barang_item WHERE ambil_barang_id = $id";
    $queryBarang = $conn->query($sqlBarang);
    $barang      = $queryBarang->fetch_assoc();
    $stockreturn   = $barang["jumlah"];
    $barangid   = $barang["barang_id"];

    $sqlBarangstock   = "SELECT * FROM stock WHERE id = $barangid";
    $queryBarangstock = $conn->query($sqlBarangstock);
    $barangstock      = $queryBarangstock->fetch_assoc();
    $stock   = $barangstock["jumlah"];
    $stockAkhir  = $barangstock["jumlah"] + (int)$stockreturn;

    $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhir WHERE id = $barangid";

        if ($conn->query($sqlUpdateStock)) {
            $sql = "DELETE FROM ambil_barang_item where ambil_barang_id = $id";
            if ($conn->query($sql)) {
            $sqldelete = "DELETE FROM ambil_barang where id = $id";
    if ($conn->query($sqldelete)) {
        echo "<script> alert('hapus SPB berhasil');document.location = 'ambilValidasi.php'; </script>";
    }
        }
    }
    
}
?>