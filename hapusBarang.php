<?php
require "authMiddleware.php";

if (isset($_GET["id"])) {
    $id  = $_GET["id"];
    $sql = "DELETE FROM stock where id = $id";
    if ($conn->query($sql)) {
        echo "<script> alert('hapus barang berhasil');document.location = 'daftarBarang.php'; </script>";
    }
}
?>