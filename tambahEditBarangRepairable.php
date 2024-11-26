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
    $PIC = $_POST['pic'];
    $keterangandisposisi = $_POST['disposisi'];    
    $potensiPemakaian = $_POST['potensi_pemakaian'];
    $bulan = date('m');
    $tahun = date('Y');

    // Cek apakah Nama_Barang sudah ada di tabel mutasi_part_bekas
    $sqlCheck = "SELECT COUNT(*) as count FROM part_repairable WHERE Nama_Barang = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $namaBarang);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $rowCheck = $resultCheck->fetch_assoc();

    if ($rowCheck['count'] > 0) {
        // Jika barang dengan Nama_Barang ada, lakukan UPDATE
        $sqlUpdate = "
            UPDATE part_repairable
            SET Masuk = Masuk + ?, 
                Saldo_Akhir = Saldo_Akhir + ? 
            WHERE Nama_Barang = ?
        ";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iis", $jumlahBaru, $jumlahBaru, $namaBarang);
        if ($stmtUpdate->execute()) {
            $response['update'] = "Data berhasil diperbarui di part_repairable.";
        } else {
            $response['update'] = "Gagal memperbarui part_repairable: " . $stmtUpdate->error;
        }
    } else {
        // Jika tidak ada barang, lakukan INSERT sebagai data baru
        $saldoAwal = 0; // Inisialisasi saldo awal
        $keluar = 0; // Inisialisasi jumlah barang keluar

        $sqlInsertMutasi = "
            INSERT INTO part_repairable 
            (`Area Penyimpanan`, Kode_lokasi, Kategori, Nama_Barang, Saldo_Awal, Masuk, Keluar, Saldo_Akhir, satuan,Keterangan_Pengajuan_Disposisi, Keterangan_Potensi_Pemakaian_Barang) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)
        ";
        $stmtInsertMutasi = $conn->prepare($sqlInsertMutasi);
        $stmtInsertMutasi->bind_param("ssssiiisis", 
            $areaPenyimpanan, $kodeLokasi, $kategori, $namaBarang, $saldoAwal, $jumlahBaru, $keluar, $jumlahBaru, $satuan,$keterangandisposisi , $potensiPemakaian
        );
        if ($stmtInsertMutasi->execute()) {
            $response['insert'] = "Data baru berhasil ditambahkan ke part_repairable.";
        } else {
            $response['insert'] = "Gagal menambahkan data baru ke part_repairable: " . $stmtInsertMutasi->error;
        }
    }

    // Insert ke barang_repairable_masuk
    $sqlInsert = "
        INSERT INTO barang_repairable_masuk
        ( `kode_lokasi`, kategori, tanggal, nama_barang, jumlah, satuan, mesin, pic, keterangan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ssssissss", 
     $kodeLokasi, $kategori,$tanggal, $namaBarang, $jumlahBaru, $satuan, $potensiPemakaian, $PIC, $keterangandisposisi
    );

    if ($stmtInsert->execute()) {
        $response['barang_repairable_masuk'] = "Data berhasil ditambahkan ke barang_repairable_masuk.";
    } else {
        $response['barang_repairable_masuk'] = "Gagal menambahkan ke barang_repairable_masuk: " . $stmtInsert->error;
    }

    header('Location: dashboard-repairable');
    exit;
}
?>
