<?php
require "authMiddleware.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$userId = $user["id"];

if ($user["role"] == 'teknisi' && $user['username'] != 'gilang') {
    header("location: content.php");
}

$sql = "SELECT * FROM barang_bekas_keluar";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <style>
    body {
        background: url("assets/images/bg-historyout-bekas.png");
        background-color: black;
        background-repeat: no-repeat;
        background-size: 100% 120%;
        background-position: center top;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="toolbar mb-3">
            <a href="content.php">
                <img src="assets/images/menu-icon.png" alt="" class="menu-icon me-3">
            </a>
            <a class="btn btn-primary" href="barang-bekas-masuk.php">Barang Masuk</a>
            <a class="btn btn-primary" href="barang-bekas.php">Dashboard</a>
            <input type="text" id="start_date" placeholder="Start Date" class="form-control d-inline w-auto">
            <input type="text" id="end_date" placeholder="End Date" class="form-control d-inline w-auto">
            <input type="text" id="item" placeholder="Item" class="form-control d-inline w-auto">
            <button id="exportBtn" class="btn btn-primary">Export</button>
        </div>
        <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
            <table class="table table-bordered table-dark border-white" id="item_table">
                <thead>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-warning" style="min-width: 100px">No</th>
                        <th class="text-warning" style="min-width: 100px">Tanggal Pengeluaran</th>
                        <th class="text-warning" style="min-width: 100px">Kode Lokasi</th>
                        <th class="text-warning" style="min-width: 100px">Kategori</th>
                        <th class="text-warning" style="min-width: 100px">Nama Barang</th>
                        <th class="text-warning" style="min-width: 100px">Jumlah</th>
                        <th class="text-warning" style="min-width: 100px">Satuan</th>
                        <th class="text-warning" style="min-width: 100px">No SPP</th>
                        <th class="text-warning" style="min-width: 100px">Peruntukan</th>
                        <th class="text-warning" style="min-width: 100px">Bulan</th>
                        <th class="text-warning" style="min-width: 100px">Tahun</th>


                    </tr>
                </thead>
                <tbody class="text-center fw-semibold">
                    <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['tanggal_pengeluaran']; ?></td>
                        <td><?php echo $row['kode_lokasi']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td><?php echo $row['satuan']; ?></td>
                        <td><?php echo $row['no_spb']; ?></td>
                        <td><?php echo $row['peruntukan']; ?></td>
                        <td><?php echo $row['bulan']; ?></td>
                        <td><?php echo $row['tahun']; ?></td>

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
                        <input type="submit" name="submit" class="btn btn-primary" value="Simpan"
                            onclick="return confirm('Apakah anda yakin ingin mengubah data tersebut?');">
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

    function showEditNo(data) {
        var modalEl = $("#nospb_modal");

        $(`input[name='id']`).val(data['id']);
        $(`input[name='barang_id']`).val(data['barang_id']);
        $(`textarea[name='nama_barang']`).val(data['nama_barang']);
        $(`input[name='no_awal']`).val(data['no_spb']);

        modalEl.modal('show');
    }
    $(document).ready(function() {
        $('#item_table').DataTable();

        // Initialize Flatpickr
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#end_date", {
            dateFormat: "Y-m-d"
        });

        // Export button click event
        $("#exportBtn").on("click", function() {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var item = $("#item").val(); // Mengambil nilai dari kolom input "item"

            if (startDate && endDate) {
                window.location.href = "export_bekas_keluar.php?start_date=" + startDate + "&end_date=" +
                    endDate + "&item=" + item;
            } else {
                alert("Please select both start and end date");
            }
        });

    });
    </script>
</body>

</html>