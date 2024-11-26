<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ambilbarangId = $_POST["id"]; 
    $noSPB   = $_POST["no"];
    $sqlupdateambil = "UPDATE ambil_barang SET no_spb = $noSPB WHERE id = $ambilbarangId";
        $result = $conn->query($sqlupdateambil);

        if ($conn->query($sqlupdateambil)) {
            echo "<script> alert('edit SPB berhasil');document.location = 'historyOut.php' </script>";
        }
}


?>
