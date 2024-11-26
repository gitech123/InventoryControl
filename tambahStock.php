<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId   = $user["id"];
    $barangId = $_POST["barang_id"];
    $jumlah   = $_POST["jumlah"];
    $tgl      = date("Y-m-d");

    $sqlBarang   = "SELECT * FROM stock WHERE id = $barangId";
    $queryBarang = $conn->query($sqlBarang);
    $barang      = $queryBarang->fetch_assoc();
    $stockAwal   = $barang["jumlah"];
    $stockAkhir  = $barang["jumlah"] + (int)$jumlah;
    $sqlMaxId = "SELECT MAX(id) as max_id FROM barang_masuk";
    $resultMaxId = $conn->query($sqlMaxId);
    $row = $resultMaxId->fetch_assoc();
    $maxId = $row['max_id'];
    $currentid = $maxId + 1;

    $sqlStock = "INSERT INTO barang_masuk values ($currentid,$barangId,$userId,$jumlah,$stockAwal,$stockAkhir,'$tgl')";

    if ($conn->query($sqlStock)) {
        $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhir WHERE id = $barangId";

        if ($conn->query($sqlUpdateStock)) {
            echo "<script> alert('tambah jumlah barang berhasil');document.location = 'daftarBarang.php' </script>";
        }
    }

}
?>