<?php
require "authMiddleware.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id                    = isset($_POST["id"]) ? $_POST['id'] : '';
    $area_penyimpanan      = isset($_POST["area_penyimpanan"]) ? $_POST['area_penyimpanan'] : '';
    $kode_item             = isset($_POST["kode_item"]) ? $_POST['kode_item'] : '';
    $kode_lokasi           = isset($_POST["kode_lokasi"]) ? $_POST['kode_lokasi'] : '';
    $kategori              = isset($_POST["kategori"]) ? $_POST['kategori'] : '';
    $tanggal               = isset($_POST["tanggal"]) ? $_POST['tanggal'] : '';
    $po                    = isset($_POST["po"]) ? $_POST['po'] : '';
    $supplier              = isset($_POST["supplier"]) ? $_POST['supplier'] : '';
    $nama_barang           = isset($_POST["nama_barang"]) ? $_POST['nama_barang'] : '';
    $jumlah                = isset($_POST["jumlah_baru"]) ? $_POST['jumlah_baru'] : 0;
    $satuan                = isset($_POST["satuan"]) ? $_POST['satuan'] : '';
    $peruntukan            = isset($_POST["peruntukan"]) ? $_POST['peruntukan'] : 0;
    $status_persamaan_nama = isset($_POST["status_persamaan_nama"]) ? $_POST['status_persamaan_nama'] : 0;
    $nama_oracle           = isset($_POST["nama_oracle"]) ? $_POST['nama_oracle'] : 0;


    if ($id == '') {
        $sqlAction = "INSERT INTO stock VALUES (
                           '',
                           '$area_penyimpanan',
                           '$kode_item',
                           '$kode_lokasi',
                           '$kategori',
                           '$nama_barang',
                           '$satuan',
                           '$jumlah',
                           '$tanggal',
                           '$po',
                           '$supplier',
                           '$peruntukan',
                           '$status_persamaan_nama',
                           '$nama_oracle',
                           '0'
                           )";

        if ($conn->query($sqlAction)) {
            $sqlMaxId = "SELECT MAX(id) as max_id FROM stock";
            $resultMaxId = $conn->query($sqlMaxId);
            $row = $resultMaxId->fetch_assoc();
            $maxId = $row['max_id'];
            $sqlMaxIdb = "SELECT MAX(id) as max_id FROM barang_masuk";
            $resultMaxIdb = $conn->query($sqlMaxIdb);
            $rowb = $resultMaxIdb->fetch_assoc();
            $maxIdb = $rowb['max_id'];
            $currentid = $maxIdb + 1;
            $sqlStock = "INSERT INTO barang_masuk values ($currentid,$maxId,'1','0','0',$jumlah,'$tanggal','$supplier','$po')";
            if ($conn->query($sqlStock)) {
            echo "<script> alert('tambah barang berhasil');document.location = 'daftarBarang.php'; </script>";
        }}
    }
    else {
            $sqlBarang   = "SELECT * FROM stock WHERE id = $id";
            $queryBarang = $conn->query($sqlBarang);
            $barang      = $queryBarang->fetch_assoc();
            $stockAwal   = $barang["jumlah"];
            $stockAkhir  = $barang["jumlah"] + (int)$jumlah;
            $sqlMaxIdb = "SELECT MAX(id) as max_id FROM barang_masuk";
            $resultMaxIdb = $conn->query($sqlMaxIdb);
            $rowb = $resultMaxIdb->fetch_assoc();
            $maxIdb = $rowb['max_id'];
            $currentid = $maxIdb + 1;
            if((int)$jumlah > 0){
            $sqlStock = "INSERT INTO barang_masuk values ($currentid,$id,'1','$jumlah','$stockAwal','$stockAkhir','$tanggal','$supplier','$po')";
            if ($conn->query($sqlStock)) {
                $sqlAction = "UPDATE stock SET 
                  area_penyimpanan = '$area_penyimpanan',
                  kode_item = '$kode_item',
                  kode_lokasi = '$kode_lokasi',
                  kategori = '$kategori',
                  tanggal = '$tanggal',
                  po = '$po',
                  supplier = '$supplier',
                  nama_barang = '$nama_barang',
                  jumlah = '$stockAkhir',
                  satuan = '$satuan',
                  peruntukan = '$peruntukan',
                  status_persamaan_nama = '$status_persamaan_nama',
                  nama_oracle = '$nama_oracle'
                    WHERE id = $id
                  ";
                  if ($conn->query($sqlAction)) {
                echo "<script> alert('edit tambah barang berhasil');document.location = 'daftarBarang.php' </script>";
                  }
                }
              }
              else{
                $sqlAction = "UPDATE stock SET 
                  area_penyimpanan = '$area_penyimpanan',
                  kode_item = '$kode_item',
                  kode_lokasi = '$kode_lokasi',
                  kategori = '$kategori',
                  tanggal = '$tanggal',
                  po = '$po',
                  supplier = '$supplier',
                  nama_barang = '$nama_barang',
                  jumlah = '$stockAkhir',
                  satuan = '$satuan',
                  peruntukan = '$peruntukan',
                  status_persamaan_nama = '$status_persamaan_nama',
                  nama_oracle = '$nama_oracle'
                    WHERE id = $id
                  ";
                  if ($conn->query($sqlAction)) {
                echo "<script> alert('edit barang berhasil');document.location = 'daftarBarang.php' </script>";
                  }
              }
            
        
    }
}
?>