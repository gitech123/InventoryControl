<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Id = $_POST["id"]; 
    $noSPB   = $_POST["no"];
    $nospblama = $_POST["no_awal"];
    $log = "#################################  Perubahan di Halaman Edit No SPB Barang Bekas Masuk  #################################################\n"; // Variabel untuk mencatat log
    $log .= $Id . " ";
    $log .= "Diubah Oleh ";
    $log .= $user["username"];
    $log .= "\n";
    $sqlupdateambil = "UPDATE barang_bekas_masuk SET `No SPB` = '$noSPB' WHERE id = $Id";
        $result = $conn->query($sqlupdateambil);
        $log .= "[" . date("Y-m-d H:i:s") . "] Nomor SPB berubah dari $nospblama menjadi $noSPB \n";

        if ($conn->query($sqlupdateambil)) {
            file_put_contents("log_user.txt", $log, FILE_APPEND);
            echo "<script> alert('edit SPB berhasil');document.location = 'barang-bekas-masuk.php' </script>";
        }
}


?>
