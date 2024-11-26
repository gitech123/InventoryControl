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
    $potensiPemakaian = $_POST['potensi_pemakaian'];
    $bulan = date('m');
    $tahun = date('Y');

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
            SET Masuk = Masuk + ?, 
                Saldo_Akhir = Saldo_Akhir + ? 
            WHERE Nama_Barang = ?
        ";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iis", $jumlahBaru, $jumlahBaru, $namaBarang);
        if ($stmtUpdate->execute()) {
            $response['update'] = "Data berhasil diperbarui di mutasi_part_bekas.";
        } else {
            $response['update'] = "Gagal memperbarui mutasi_part_bekas: " . $stmtUpdate->error;
        }
    } else {
        // Jika tidak ada barang, lakukan INSERT sebagai data baru
        $saldoAwal = 0; // Inisialisasi saldo awal
        $keluar = 0; // Inisialisasi jumlah barang keluar

        $sqlInsertMutasi = "
            INSERT INTO mutasi_part_bekas 
            (`Area Penyimpanan`, Kode_lokasi, Kategori, Nama_Barang, Saldo_Awal, Masuk, Keluar, Saldo_Akhir, satuan, Keterangan_Potensi_Pemakaian_Barang) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmtInsertMutasi = $conn->prepare($sqlInsertMutasi);
        $stmtInsertMutasi->bind_param("ssssiiisis", 
            $areaPenyimpanan, $kodeLokasi, $kategori, $namaBarang, $saldoAwal, $jumlahBaru, $keluar, $jumlahBaru, $satuan, $potensiPemakaian
        );
        if ($stmtInsertMutasi->execute()) {
            $response['insert'] = "Data baru berhasil ditambahkan ke mutasi_part_bekas.";
        } else {
            $response['insert'] = "Gagal menambahkan data baru ke mutasi_part_bekas: " . $stmtInsertMutasi->error;
        }
    }

    // Insert ke barang_bekas_masuk
    $sqlInsert = "
        INSERT INTO barang_bekas_masuk 
        (`Area Penyimpanan`, `Kode Lokasi`, Kategori, Tanggal, `Nama Barang`, Jml, Uom, `No Spb`, Bulan, Tahun) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sssssisssi", 
        $areaPenyimpanan, $kodeLokasi, $kategori,$tanggal, $namaBarang, $jumlahBaru, $satuan, $noSpb, $bulan, $tahun
    );

    if ($stmtInsert->execute()) {
        $response['barang_bekas_masuk'] = "Data berhasil ditambahkan ke barang_bekas_masuk.";
    } else {
        $response['barang_bekas_masuk'] = "Gagal menambahkan ke barang_bekas_masuk: " . $stmtInsert->error;
    }

    header('Location: barang-bekas.php');
    exit;
}
?>
