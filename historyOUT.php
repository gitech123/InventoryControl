<?php
require "authMiddleware.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$userId = $user["id"];

if ($user["role"] == 'teknisi' && $user['username'] != 'gilang' && $user['username'] != 'Aries Nugraha') {
    header("location: content.php");
}

$sql = "SELECT
	ambil_barang.id, 
    ambil_barang_item.id as id_ambil,
	ambil_barang.teknisi_id, 
	ambil_barang.`no`, 
	ambil_barang.tgl_permintaan, 
	ambil_barang.keperluan, 
	ambil_barang.tgl_dibutuhkan, 
	ambil_barang.dikirim_ke, 
	ambil_barang.`status`, 
	ambil_barang.`tgl_validasi`, 
    ambil_barang.no_spb, 
    ambil_barang.bulan_spb, 
    ambil_barang.tahun_spb, 
	stock.nama_barang, 
    stock.kode_lokasi,
    stock.kategori,
	ambil_barang_item.satuan, 
	ambil_barang_item.jumlah,
    ambil_barang_item.barang_id,
    ambil_barang_item.pengembalian
FROM
	ambil_barang
	INNER JOIN
	ambil_barang_item
	ON 
		ambil_barang.id = ambil_barang_item.ambil_barang_id
	INNER JOIN
	stock
	ON 
		ambil_barang_item.barang_id = stock.id
    ORDER BY
    ambil_barang.id DESC
		
		";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <style>
        body {
            background: url("assets/images/bg-history.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-position: center top;
        }
        .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid blue;
        width: 120px;
        height: 120px;
        animation: spin 1s linear infinite;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>
<body>
<div class="loader" id="loader"></div>
<div class="container-fluid" style="display: none; margin-top: 40px;" id="main-content">
    <div class="toolbar mb-3">
        <a href="content.php">
            <img src="assets/images/menu-icon.png" alt="" class="menu-icon me-3">
        </a>
        <input type="text" id="start_date" placeholder="Start Date" class="form-control d-inline w-auto">
        <input type="text" id="end_date" placeholder="End Date" class="form-control d-inline w-auto">
        <input type="text" id="item" placeholder="Item" class="form-control d-inline w-auto">
        <button id="exportBtn" class="btn btn-primary">Export</button>
    </div>
    <div style="margin:80px 40px 0 30px;height: 450px;overflow-y: auto; overflow-x: hidden">
        <table class="table table-bordered table-dark border-white" id="item_table">
            <thead>
            <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                <th class="text-warning" style="min-width: 10px">No</th>
                <th class="text-warning" style="min-width: 10px">Tanggal Pengeluaran</th>
                <th class="text-warning" style="min-width: 10px">Kode Lokasi</th>
                <th class="text-warning" style="min-width: 10px">Nama Barang</th>
                <th class="text-warning" style="min-width: 10px">Jumlah</th>
                <th class="text-warning" style="min-width: 10px">Satuan</th>
                <th class="text-warning" style="min-width: 10px">No SPB</th>
                <th class="text-warning" style="min-width: 10px">Peruntukan</th>
                <th class="text-warning" style="min-width: 10px">Nama Pengambil</th>
                <th class="text-warning" style="min-width: 10px">Status Pengembalian Barang Bekas</th>
                <?php if ($user["role"] == "admin" || $user["role"] == 'superuser' || $user['username'] == 'gilang' || $user['role'] == 'supervisorWarehouse'): ?>
                <th class="text-warning" style="min-width: 100px">Edit</th>
                <?php endif ?>

            </tr>
            </thead>
            <tbody class="text-center fw-semibold">
            <?php if ($result->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['tgl_permintaan']; ?></td>
                        <td><?php echo $row['kode_lokasi']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td><?php echo $row['satuan']; ?></td>
                        <td id="no_spb"><?php 
                        if ($row['kategori'] == 'Non Stock')
                        {echo 'GSP/'.$row['no_spb'].'/'.$row['bulan_spb'].'/'.$row['tahun_spb'].'/NS'; }
                        else {echo $row['no_spb'].'/'.'BPB/3S1/'.$row['bulan_spb'].'/'.$row['tahun_spb'];}?></td>
                        <td><?php echo $row['keperluan']; ?></td>
                        <td><?php echo $row['teknisi_id']; ?></td>
                        <?php if ($row['pengembalian'] == 1): ?>
                        <td>Sudah Kembali
                        
                        </td>
                        <?php elseif ($row['pengembalian'] == 0): ?>
                        <td>Belum Kembali
                        <div class="d-flex">                   
                                    <button class="btn btn-outline-warning mb-2" style="line-height: 0.5em" onclick='sudahkembali(<?php echo json_encode($row) ?>)'>Sudah&nbsp;Dikembalika&nbsp;
                                    </button>&nbsp
                                </div>
                        </td>
                        <?php else: ?>
                        <td>Tidak Ada Data</td>
                        <?php endif; ?>
                        <?php if ($user["role"] == "admin" || $user["role"] == 'superuser' || $user['username'] == 'gilang' || $user['role'] == 'supervisorWarehouse'): ?>
                            <td class=" text-start">
                                <div class="d-flex">                   
                                    <button class="btn btn-outline-warning mb-2" style="line-height: 0.5em" onclick='showEdit(<?php echo json_encode($row) ?>)'>Edit&nbsp;Jumlah
                                    </button>&nbsp
                                </div>
                                <div class="d-flex">                   
                                    <button class="btn btn-outline-danger mb-2" style="line-height: 0.5em" onclick='showEditNo(<?php echo json_encode($row) ?>)'>Edit&nbsp;No SPB
                                    </button>&nbsp
                                </div>
                            </td>
                            <?php endif ?>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->

<div class="modal fade" id="stock_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="EditSPB.php" method="POST" id="form_stock">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="form_barang_modal_title">Edit SPB</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="barang_id">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Nama Barang</label>
                            <textarea name="nama_barang" class="form-control" rows="3" disabled></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Jumlah Awal</label>
                            <input type="text" name="jumlah_awal" class="form-control form-control-sm" disabled>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Jumlah Akhir</label>
                            <input type="text" name="jumlah" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <input type="submit" name="submit" class="btn btn-primary" value="Simpan" onclick="return confirm('Apakah anda yakin ingin mengubah data tersebut?');">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_bekas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="EditBekas.php" method="POST" id="form_stock">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="form_barang_modal_title">Edit SPB</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="barang_id">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Nama Barang</label>
                            <textarea name="nama_barang" class="form-control" rows="3" disabled></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">NO SPB</label>
                            <input type="text" name="no_spb_" class="form-control" rows="3"></inp>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="fw-semibold mb-1">Bulan SPB</label>
                            <input type="text" name="bulan_" class="form-control" rows="2"></inp>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="fw-semibold mb-1">Tahun SPB</label>
                            <input type="text" name="tahun_" class="form-control" rows="2" ></inp>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Jumlah Barang Bekas</label>
                            <input type="text" name="jumlah_bekas" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Kode Lokasi</label>
                            <input type="text" name="kode_lokasi_bekas" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <input type="submit" name="submit" class="btn btn-primary" value="Simpan" onclick="return confirm('Apakah anda yakin ingin mengubah data tersebut?');">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="nospb_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="EditNoSPB.php" method="POST" id="form_stock">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="form_barang_modal_title">Edit No SPB</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">Nama Barang</label>
                            <textarea name="nama_barang" class="form-control" rows="3" disabled></textarea>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">SPB Awal</label>
                            <input type="text" name="no_awal" class="form-control form-control-sm" disabled>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="fw-semibold mb-1">SPB Akhir</label>
                            <input type="text" name="no" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <input type="submit" name="submit" class="btn btn-primary" value="Simpan">
                </div>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="plugins/DataTables/datatables.min.js"></script>
<script src="assets/js/flatpickr.min.js"></script>

<script>
    function showEdit(data) {
        var modalEl = $("#stock_modal");

        $(`input[name='id']`).val(data['id']);
        $(`input[name='barang_id']`).val(data['barang_id']);
        $(`textarea[name='nama_barang']`).val(data['nama_barang']);
        $(`input[name='jumlah_awal']`).val(data['jumlah']);

        modalEl.modal('show');
    }
    function sudahkembali(data) {
    console.log("Data received:", data); // Log data untuk memastikan isi

    var modalEl = $("#modal_bekas");
    var nospb = 0;

    // Pastikan kategori dan no_spb ada
    if (data['kategori'] == 'Non Stock') {
        nospb = 'GSP/' + data['no_spb'] + '/' + data['bulan_spb'] + '/' + data['tahun_spb'] + '/NS';
    } else {
        nospb = data['no_spb'] + '/BPB/3S1/' + data['bulan_spb'] + '/' + data['tahun_spb'];
    }
    console.log("Generated nospb:", nospb); // Log nospb untuk debugging

    $(`#modal_bekas input[name='no_spb_']`).val(nospb); 
    // Cek apakah input dengan name="no_spb" ada
    if ($(`input[name='no_spb']`).length === 0) {
        console.error("Input with name='no_spb' not found!");
    }

    // Isi nilai ke input
    $(`#modal_bekas input[name='bulan_']`).val(data['bulan_spb']);
    $(`#modal_bekas input[name='tahun_']`).val(data['tahun_spb']);
    $(`#modal_bekas input[name='id']`).val(data['id_ambil']);
    $(`#modal_bekas input[name='barang_id']`).val(data['barang_id']);
    $(`#modal_bekas textarea[name='nama_barang']`).val(data['nama_barang']);

    modalEl.modal('show');
}

    function showEditNo(data) {
        var modalEl = $("#nospb_modal");

        $(`input[name='id']`).val(data['id']);
        $(`input[name='barang_id']`).val(data['barang_id']);
        $(`textarea[name='nama_barang']`).val(data['nama_barang']);
        $(`input[name='no_awal']`).val(data['no_spb']);

        modalEl.modal('show');
    }
    $(document).ready(function () {
        $("#loader").hide();
        $("#main-content").show();
        $('#item_table').DataTable();
        
        // Initialize Flatpickr
        flatpickr("#start_date", {dateFormat: "Y-m-d"});
        flatpickr("#end_date", {dateFormat: "Y-m-d"});

        // Export button click event
        $("#exportBtn").on("click", function() {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var item = $("#item").val(); // Mengambil nilai dari kolom input "item"

            if (startDate && endDate) {
                window.location.href = "export.php?start_date=" + startDate + "&end_date=" + endDate + "&item=" + item;
            } else {
                alert("Please select both start and end date");
            }
        });

    });
</script>
</body>
</html>