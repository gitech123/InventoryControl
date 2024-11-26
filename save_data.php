<?php
// file: save_data.php

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sparman_fix";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$databarang = $_POST['databarang'];

// SQL INSERT query
$sqlAmbilBarangItem = "INSERT INTO simpan_id_sementara VALUES ('','$databarang')";

if ($conn->query($sqlAmbilBarangItem) === TRUE) {
    echo "<script> alert('Tambah Keranjang Berhasil')</script>";
} else {
    echo "Error: " . $sqlAmbilBarangItem . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>