<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
$log = "#################################  Perubahan di Halaman Edit No SPB Barang Bekas Masuk  #################################################\n"; // Variabel untuk mencatat log
$log .= $Id . " ";
$log .= "Diubah Oleh ";
$log .= $user["username"];
$log .= "\n";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ambilbarangId = $_POST["id"]; 
    $noSPB   = $_POST["no"];
    $nospblama = $_POST["no_awal"];
    $sqlupdateambil = "UPDATE ambil_barang SET no_spb = $noSPB WHERE id = $ambilbarangId";
    $log .= "[" . date("Y-m-d H:i:s") . "] Nomor SPB berubah dari $nospblama menjadi $noSPB \n";
        $result = $conn->query($sqlupdateambil);

        if ($conn->query($sqlupdateambil)) {
            file_put_contents("log_user.txt", $log, FILE_APPEND);
            echo "<script> alert('edit SPB berhasil');document.location = 'historyOut.php' </script>";
        }
}


?>
