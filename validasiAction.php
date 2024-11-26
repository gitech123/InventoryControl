<?php
require "authMiddleware.php";

if (isset($_GET['ambil_barang_id'])) {
    $ambilBarangId = $_GET['ambil_barang_id'];
    $tglSekarang   = date("Y-m-d");
    $userId = $user["id"];
    
    $sqlAction = "UPDATE ambil_barang SET 
                  status = 'divalidasi',
                  tgl_validasi = '$tglSekarang', validator = '$userId'
                    WHERE id = $ambilBarangId
                  ";

    if ($conn->query($sqlAction)) {
        echo "<script> alert('validasi ambil barang berhasil');document.location = 'ambilValidasi.php' </script>";
    }
}
else {
    if ($conn->query($sqlAction)) {
        echo "<script> document.location = 'ambilValidasi.php' </script>";
    }
}
?>