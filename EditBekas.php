<?php
require "authMiddleware.php";
date_default_timezone_set("Asia/Jakarta");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Id = $_POST["id"];
    $barangId = $_POST["barang_id"];
    $noSpb = $_POST["no_spb_"];
    $bulan_spb = $_POST["bulan_"];
    $tahun_spb = $_POST["tahun_"];
    $jumlah   = $_POST["jumlah_bekas"];
    $kodelokasi = $_POST["kode_lokasi_bekas"];

    $tgl      = date("Y-m-d");

    $sqlBarang   = "SELECT * FROM stock WHERE id = $barangId";
    $queryBarang = $conn->query($sqlBarang);
    $barang      = $queryBarang->fetch_assoc();
    $nama_barang = $barang["nama_barang"];
    $satuan = $barang["satuan"];
    
        $sqlinputbekas = "INSERT INTO barang_bekas_masuk VALUES(
                    '',
                    'Gudang Sparepart',
                    '$kodelokasi',
                    'Part Bekas UR',
                    '$tgl',
                    '$nama_barang',
                    '$jumlah',
                    '$satuan',
                    '$noSpb',
                    '$bulan_spb',
                    '$tahun_spb'

        )";

            //Verifikasi barang ada
            $sqlCheck = "SELECT COUNT(*) as count FROM mutasi_part_bekas WHERE Nama_Barang = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bind_param("s", $nama_barang);
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
                $stmtUpdate->bind_param("iis", $jumlah, $jumlah, $nama_barang);
                if ($stmtUpdate->execute()) {
                    $response['update'] = "Data berhasil diperbarui di mutasi_part_bekas.";
                } else {
                    $response['update'] = "Gagal memperbarui mutasi_part_bekas: " . $stmtUpdate->error;
                }
            } else {
                // Jika tidak ada barang, lakukan INSERT sebagai data baru
                $saldoAwal = 0; // Inisialisasi saldo awal
                $keluar = 0; // Inisialisasi jumlah barang keluar
                $lokasi = "Gudang Sparepart";
                $kat = "Part Bekas UR";
                $keterangan = "Bukti Pembelian Barang";
                $sqlInsertMutasi = "
                    INSERT INTO mutasi_part_bekas 
                    (`Area Penyimpanan`, Kode_lokasi, Kategori, Nama_Barang, Saldo_Awal, Masuk, Keluar, Saldo_Akhir, satuan, Keterangan_Potensi_Pemakaian_Barang) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";
                $stmtInsertMutasi = $conn->prepare($sqlInsertMutasi);
                $stmtInsertMutasi->bind_param("ssssiiiiss", 
                    $lokasi, $kodelokasi, $kat, $nama_barang, $saldoAwal, $jumlah, $keluar, $jumlah, $satuan, $keterangan
                );
                if ($stmtInsertMutasi->execute()) {
                    $response['insert'] = "Data baru berhasil ditambahkan ke mutasi_part_bekas.";
                } else {
                    $response['insert'] = "Gagal menambahkan data baru ke mutasi_part_bekas: " . $stmtInsertMutasi->error;
                }
            }
            if ( $conn->query($sqlinputbekas)) {
                $sqlupdate = "UPDATE ambil_barang_item SET pengembalian = 1 WHERE id = $Id";
                if($conn->query($sqlupdate)){
                echo "<script> alert('Verifikasi Kedatangan Barang Bekas Berhasil');document.location = 'HistoryOUT.php' </script>";
            }}

























}