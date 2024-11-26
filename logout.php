<?php
session_start();
require "authMiddleware.php";
$sqlHapusBarang = "DELETE FROM simpan_id_sementara ";

if ($conn->query($sqlHapusBarang) === TRUE) {
    echo "Records deleted successfully";
} else {
    http_response_code(500);
    echo "Error deleting records: " . $conn->error;
}

// Close connection
$conn->close();
session_destroy();

header("location: login.php");
?>