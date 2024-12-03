<?php
require "authMiddleware.php";
$log = "#################################  Perubahan di Halaman Hapus Barang  #################################################\n"; // Variabel untuk mencatat log
$log .= $Id . " ";
$log .= "Diubah Oleh ";
$log .= $user["username"];
$log .= "\n";
if (isset($_GET["id"])) {
    $id  = $_GET["id"];
    $sql = "DELETE FROM stock where id = $id";
    $log .= "[" . date("Y-m-d H:i:s") . "] Barang dari database Delete `barang_masuk` - id: $id\n";

    if ($conn->query($sql)) {
        file_put_contents("log_user.txt", $log, FILE_APPEND);
        echo "<script> alert('hapus barang berhasil');document.location = 'daftarBarang.php'; </script>";
    }
}
?>