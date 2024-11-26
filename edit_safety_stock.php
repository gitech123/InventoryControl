<?php
require "authMiddleware.php"; // Pastikan pengguna terautentikasi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai dari formulir
    $barangId = $_POST['barang_id'];
    $safetyStock = $_POST['safety_stock'];

    // Siapkan query untuk memperbarui safety stock
    $sqlUpdate = "UPDATE stock SET safety_stock = ? WHERE id = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("ii", $safetyStock, $barangId); // ii untuk integer

    if ($stmt->execute()) {
        // Jika berhasil, redirect kembali ke halaman sebelumnya dengan pesan sukses
        header("Location: dashboard.php?success=1");
    } else {
        // Jika gagal, redirect dengan pesan error
        header("Location: dashboard.php?error=1");
    }
}
?>
