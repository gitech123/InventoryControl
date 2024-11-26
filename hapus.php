
<?php
// file: hapus.php

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

// Retrieve data from POST request
$barangIds = $_POST['barangIds'];

// Convert barangIds to array
$barangIdsArray = explode(',', $barangIds);
$barangIdsString = implode("','", $barangIdsArray);

// SQL DELETE query
$sqlHapusBarang = "DELETE FROM simpan_id_sementara ";

if ($conn->query($sqlHapusBarang) === TRUE) {
    echo "Records deleted successfully";
} else {
    http_response_code(500);
    echo "Error deleting records: " . $conn->error;
}

// Close connection
$conn->close();
?>
