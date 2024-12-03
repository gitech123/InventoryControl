<?php
require "authMiddleware.php";
$log = "#################################  Perubahan di Halaman Hapus SPB  #################################################\n"; // Variabel untuk mencatat log
$log .= $Id . " ";
$log .= "Diubah Oleh ";
$log .= $user["username"];
$log .= "\n";
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
    $nama_barang = $barangstock["nama_barang"];
    $stockAkhir  = $barangstock["jumlah"] + (int)$stockreturn;

    $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhir WHERE id = $barangid";
    $log .= "[" . date("Y-m-d H:i:s") . "] SPB di hapus atau di cancel. Nama Barang : $nama_barang, ID Barang : $barangid, Jumlah : $stockreturn, \n";

        if ($conn->query($sqlUpdateStock)) {
            $sql = "DELETE FROM ambil_barang_item where ambil_barang_id = $id";
            if ($conn->query($sql)) {
            $sqldelete = "DELETE FROM ambil_barang where id = $id";
    if ($conn->query($sqldelete)) {
        file_put_contents("log_user.txt", $log, FILE_APPEND);
        echo "<script> alert('hapus SPB berhasil');document.location = 'ambilValidasi.php'; </script>";
    }
        }
    }
    
}
?>