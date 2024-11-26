<?php
require "authMiddleware.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";

    $userId         = $user['id'];
    $barang_ids     = explode(",", $_POST['barang_id']);
    $no             = $_POST["no"];
    $tgl_permintaan = $_POST["tgl_permintaan"];
    $keperluan      = $_POST["keperluan"];
    $tgl_dibutuhkan = "0";//$_POST["tgl_dibutuhkan"];
    $dikirim_ke     = $_POST["dikirim_ke"];
    $barang_satuan  = $_POST["satuan"];
    $barang_jumlah  = $_POST["jumlah"];
    $nama_teknisi = $_POST["nama_teknisi"];
    $startDateTime = new DateTime($tgl_permintaan);
    // Mendapatkan bulan dan tahun
    $bulan_spb = $startDateTime->format("m"); // Output: "11"
    $tahun_spb = $startDateTime->format("Y");  // Output: "2024"
    //$bulan_spb = date("m");
    //$tahun_spb = date("Y");
    $romawi_bulan = array(
        '01' => "I",
        '02' => "II",
        '03' => "III",
        '04' => "IV",
        '05' => "V",
        '06' => "VI",
        '07' => "VII",
        '08' => "VIII",
        '09' => "IX",
        '10' => "X",
        '11' => "XI",
        '12' => "XII"
    );
    $sqlKategori = "SELECT kategori FROM stock WHERE id = $barang_ids[0]";
    $ResultKategori   = $conn->query($sqlKategori);
    $Kategori         = $ResultKategori->fetch_assoc();
    $DataKategori = $Kategori["kategori"];
    
    $bulan_romawi = $romawi_bulan[$bulan_spb];
    if($DataKategori == "Non Stock")
    {
        $sqlMaxno = "SELECT MAX(no_spb) as spb_baru FROM ambil_barang WHERE bulan_spb = $bulan_spb AND tahun_spb = $tahun_spb AND kategori_barang = '$DataKategori'";
    }
    else
    {
        $sqlMaxno = "SELECT MAX(no_spb) as spb_baru FROM ambil_barang WHERE bulan_spb = '$bulan_romawi' AND tahun_spb = $tahun_spb AND kategori_barang = '$DataKategori'";
    }
    $resultMaxno = $conn->query($sqlMaxno);
    
    if ($resultMaxno) {
        $row = $resultMaxno->fetch_assoc();
        $maxno = $row['spb_baru'];
        if ($maxno === null) {
            $currentno = 1; // Jika tidak ada data, tetapkan nomor mulai dari 1
        } else {
            $currentno = $maxno + 1;
        }
    } else {
        // Tangani kesalahan query di sini jika diperlukan
        $currentno = 1; // Tetapkan nilai default jika terjadi kesalahan dalam query
    }
    
    // Sekarang Anda dapat menggunakan $currentno sesuai kebutuhan Anda


    //insert ke tabel ambil_barang
    $sqlHapusBarang = "DELETE FROM simpan_id_sementara ";
    if($DataKategori == "Non Stock")
    {
    $sqlAmbilBarang = "INSERT INTO ambil_barang VALUES (
                           '',
                           '$nama_teknisi',
                           '$no',
                           '$tgl_permintaan',
                           '$keperluan',
                           '$tgl_dibutuhkan',
                           '$dikirim_ke',
                           'pending',
                            null,
                            '$currentno',
                            '$bulan_spb',
                            '$tahun_spb',
                            '$DataKategori',
                            '',
                            ''
                           )";
    }
    else {
        $sqlAmbilBarang = "INSERT INTO ambil_barang VALUES (
            '',
            '$nama_teknisi',
            '$no',
            '$tgl_permintaan',
            '$keperluan',
            '$tgl_dibutuhkan',
            '$dikirim_ke',
            'pending',
             null,
             '$currentno',
             '$bulan_romawi',
             '$tahun_spb',
             '$DataKategori',
             '',
             ''
            )";
    }
    if ($conn->query($sqlAmbilBarang)) {
        $sqlHapusBarang = "DELETE FROM simpan_id_sementara ";
        $sqlAmbilBarangTerakhir = "SELECT * FROM ambil_barang ORDER BY id DESC LIMIT 1";
        $barangTerakhirResult   = $conn->query($sqlAmbilBarangTerakhir);
        $barangTerakhir         = $barangTerakhirResult->fetch_assoc();
        foreach ($barang_ids as $barang_id) {
            $ambilBarangId = $barangTerakhir["id"];
            $satuan        = $_POST["satuan"][$barang_id];
            $jumlah     = $_POST["jumlah"][$barang_id];
            $ada = isset($_POST['Ada']) && in_array($barang_id, $_POST['Ada']);
            $sqlBarang   = "SELECT * FROM stock WHERE id = $barang_id";
            $queryBarang = $conn->query($sqlBarang);
            $barang      = $queryBarang->fetch_assoc();
            $namabarang = $barang["nama_barang"];
            $stockAwal   = $barang["jumlah"];
            
            $stockAkhir  = $barang["jumlah"] - (int)$jumlah;
            if($ada == 1){
                $kodelokasi = $_POST["kode_lokasi"][$barang_id];
                $barangbekas = $_POST["jumlahbarangbekas"][$barang_id];
                //Verifikasi barang ada
                $sqlCheck = "SELECT COUNT(*) as count FROM mutasi_part_bekas WHERE Nama_Barang = ?";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bind_param("s", $namabarang);
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
                    $stmtUpdate->bind_param("iis", $barangbekas, $barangbekas, $namabarang);
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
                        $lokasi, $kodelokasi, $kat, $namabarang, $saldoAwal, $barangbekas, $keluar, $barangbekas, $satuan, $keterangan
                    );
                    if ($stmtInsertMutasi->execute()) {
                        $response['insert'] = "Data baru berhasil ditambahkan ke mutasi_part_bekas.";
                    } else {
                        $response['insert'] = "Gagal menambahkan data baru ke mutasi_part_bekas: " . $stmtInsertMutasi->error;
                    }
                }
            $sqlAmbilBarangItem = "INSERT INTO ambil_barang_item VALUES (
                           '',
                           '$ambilBarangId',
                           '$barang_id',
                           '',
                           '',
                           '$satuan',
                           '$jumlah',
                           '$ada'
                           )";
            if ($DataKategori == "Non Stock") {
            $sqlinputbekas = "INSERT INTO barang_bekas_masuk VALUES(
                        '',
                        'Gudang Sparepart',
                        '$kodelokasi',
                        'Part Bekas UR',
                        '$tgl_permintaan',
                        '$namabarang',
                        '$barangbekas',
                        '$satuan',
                        'GSP/$currentno/$bulan_spb/$tahun_spb/NS',
                        '$bulan_spb',
                        '$tahun_spb'

            )";
            }
            else {
                $sqlinputbekas = "INSERT INTO barang_bekas_masuk VALUES(
                            '',
                            'Gudang Sparepart',
                            '$kodelokasi',
                            'Part Bekas UR',
                            '$tgl_permintaan',
                            '$namabarang',
                            '$barangbekas',
                            '$satuan',
                            '$currentno/BPB/3S1/$bulan_romawi/$tahun_spb',
                            '$bulan_spb',
                            '$tahun_spb'
    
                )";
                }
            }
            else{
                $sqlAmbilBarangItem = "INSERT INTO ambil_barang_item VALUES (
                               '',
                               '$ambilBarangId',
                               '$barang_id',
                               '',
                               '',
                               '$satuan',
                               '$jumlah',
                               '0'
                               )";
                }               

            if ($conn->query($sqlAmbilBarangItem)) {
                $sqlUpdateStock = "UPDATE stock SET jumlah = $stockAkhir WHERE id = $barang_id";
                $sqlHapusBarang = "DELETE FROM simpan_id_sementara ";
                $conn->query($sqlHapusBarang);
                $conn->query($sqlUpdateStock);
                if($ada == 1){
                $conn->query($sqlinputbekas);
                }
            }
        }
        //echo "<script> alert('ambil barang berhasil');document.location = 'daftarBarang.php'; </script>";
        if ($DataKategori == "Non Stock") {
            echo "<script>alert('NO SPB ANDA = GSP/$currentno/$bulan_spb/$tahun_spb/NS'); document.location = 'daftarBarang.php'; </script>";
        } else {
            echo "<script>alert('NO SPB ANDA = $currentno/BPB/3S1/$bulan_romawi/$tahun_spb'); document.location = 'daftarBarang.php'; </script>";
        }
    }
}
?>