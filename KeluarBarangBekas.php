<?php
require "authMiddleware.php";

$response = []; // Array untuk menyimpan respon

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['nama_barang'];
    $areaPenyimpanan = $_POST['area_penyimpanan'];
    $kodeLokasi = $_POST['kode_lokasi'];
    $kategori = $_POST['kategori'];
    $tanggal = $_POST['tanggal'];
    $jumlahBaru = $_POST['jumlah_baru'];
    $satuan = $_POST['satuan'];
    $noSpb = $_POST['no_spb'];
    $peruntukan = $_POST['peruntukan'];
    //$bulan = date('m');
    //$tahun = date('Y');
    $startDateTime = new DateTime($tanggal);
    $endDateTime = new DateTime($tanggal);

    // Mendapatkan bulan dan tahun
    $bulan = $startDateTime->format("m"); // Output: "11"
    $tahun = $startDateTime->format("Y");  // Output: "2024"
    // Cek apakah Nama_Barang sudah ada di tabel mutasi_part_bekas
    $sqlCheck = "SELECT COUNT(*) as count FROM mutasi_part_bekas WHERE Nama_Barang = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $namaBarang);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $rowCheck = $resultCheck->fetch_assoc();

    if ($rowCheck['count'] > 0) {
        // Jika barang dengan Nama_Barang ada, lakukan UPDATE
        $sqlUpdate = "
            UPDATE mutasi_part_bekas 
            SET Keluar = Keluar + ?, 
                Saldo_Akhir = Saldo_Akhir - ? 
            WHERE Nama_Barang = ?
        ";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iis", $jumlahBaru, $jumlahBaru, $namaBarang);
        if ($stmtUpdate->execute()) {
            $response['update'] = "Data berhasil diperbarui di mutasi_part_bekas.";
        } else {
            $response['update'] = "Gagal memperbarui mutasi_part_bekas: " . $stmtUpdate->error;
        }
    }
    // Insert ke barang_bekas_keluar
    $sqlInsert = "
        INSERT INTO barang_bekas_keluar 
        (tanggal_pengeluaran,`kode_lokasi`, kategori, nama_barang, jumlah, satuan, no_spb, peruntukan, bulan, tahun) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sssssisssi", 
        $tanggal, $kodeLokasi, $kategori,$namaBarang, $jumlahBaru, $satuan, $noSpb, $peruntukan, $bulan, $tahun
    );

    if ($stmtInsert->execute()) {
        $response['barang_bekas_keluar'] = "Data berhasil ditambahkan ke barang_bekas_masuk.";
    } else {
        $response['barang_bekas_keluar'] = "Gagal menambahkan ke barang_bekas_masuk: " . $stmtInsert->error;
    }

    header('Location: barang-bekas.php');
    exit;
}
?>