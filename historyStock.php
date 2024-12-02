<?php
require "authMiddleware.php";
require 'vendor/autoload.php';
//if ($user["user"] != 'supervisor') {
//    header("location: content.php");
//}

$sql = "SELECT
    barang_masuk.id,
    barang_masuk.jumlah, 
    barang_masuk.jumlah_awal, 
    barang_masuk.jumlah_akhir, 
    barang_masuk.tgl, 
    stock.area_penyimpanan,
    stock.kode_lokasi,
    stock.kategori,
    stock.tanggal,
    stock.po,
    stock.supplier,
    barang_masuk.po_masuk,
    barang_masuk.supplier_masuk,
    stock.nama_barang,
    stock.peruntukan,
    barang_masuk.barang_id, 
    barang_masuk.user_id, 
    `user`.username
FROM
    barang_masuk
INNER JOIN
    stock ON barang_masuk.barang_id = stock.id
INNER JOIN
    `user` ON barang_masuk.user_id = `user`.id
ORDER BY
    barang_masuk.id DESC
";

$result = $conn->query($sql);
?>

<html>
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

        .btn-request {
            background: #ea2e2e;
            color: #fff;
            border: 1px solid #EA2E2EFF;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="toolbar mb-3">
        <a href="content.php">
            <img src="assets/images/menu-icon.png" alt="" class="menu-icon me-3">
        </a>
        <input type="text" id="start_date" placeholder="Start Date" class="form-control d-inline w-auto">
        <input type="text" id="end_date" placeholder="End Date" class="form-control d-inline w-auto">
        <button id="exportBtn" class="btn btn-primary">Export</button>
    </div>
    </div>
    <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
        <table
                class="table table-bordered table-dark border-white"
                id="item_table"
        >
            <thead>
            <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                <th class="text-warning" >No</th>
                <th class="text-warning" >Area Penyimpanan</th>
                <th class="text-warning" >Kode Lokasi</th>
                <th class="text-warning" >Kategori</th>
                <th class="text-warning" >Tanggal</th>
                <th class="text-warning" >PO</th>
                <th class="text-warning" >Supplier</th>
                <th class="text-warning" >Nama Barang</th>
                <th class="text-warning" >Jml Awal</th>
                <th class="text-warning" >Jml Masuk</th>
                <th class="text-warning" >Jml Akhir</th>
                <th class="text-warning" >Tanggal Masuk</th>
                <th class="text-warning" >Aksi</th>
            </tr>
            </thead>
            <tbody class="text-center fw-semibold">
            <?php if ($result->num_rows > 0): ?>
                <?php $no = 1; // Inisialisasi nomor urut ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td> <!-- Nomor urut -->
                       <!-- <td>//<//?php echo $row['id']; ?></td> -->
                        <td><?php echo $row['area_penyimpanan']; ?></td>
                        <td><?php echo $row['kode_lokasi']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <td><?php echo $row['tgl']; ?></td>
                        <td><?php echo $row['po_masuk']; ?></td>
                        <td><?php echo $row['supplier_masuk']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['jumlah_awal']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td><?php echo $row['jumlah_akhir']; ?></td>
                        <td><?php echo $row['tgl']; ?></td>
                        <?php if ($user["role"] == "admin" || $user['role'] == 'superuser' || $user['role'] == 'supervisorWarehouse'): ?>
                            <td class=" text-start">
                                <div class="d-flex">
                                    <a class="btn btn-outline-danger mb-2" href="hapusHistory.php?id=
                                    <?php 
                                    echo $row['id'];
                                    ?>" onclick="
                return confirm('Apakah anda yakin ingin menghapus data tersebut?');" style="line-height: 0.5em">Hapus</a>
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

<div class="modal fade" id="daftar_barang_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="form_barang_modal_title">Daftar Barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table
                        class="table table-bordered table-dark border-white"
                >
                    <thead>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-warning">Nama Barang</th>
                        <th class="text-warning">Satuan</th>
                        <th class="text-warning">Jumlah</th>
                    </tr>
                    </thead>
                    <tbody class="text-center fw-semibold" id="daftar_barang_table"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="plugins/DataTables/datatables.min.js"></script>
<script src="assets/js/flatpickr.min.js"></script>
<script>
    $(document).ready(function () {
        $('#item_table').DataTable();

        $("[data-action='show-barang']").click(function () {
            let modalBarang = $("#daftar_barang_modal");
            let tableBarang = $("#daftar_barang_table");
            let barang = $(this).data('barang');
            tableBarang.html("");

            barang.forEach(b => {
                tableBarang.append(`<tr>
    <td>${b.nama_barang}</td>
    <td>${b.satuan}</td>
    <td>${b.jumlah}</td>
</tr>`);
            })

            modalBarang.modal("show");

        });
        flatpickr("#start_date", {dateFormat: "Y-m-d"});
        flatpickr("#end_date", {dateFormat: "Y-m-d"});

        // Export button click event
        $("#exportBtn").on("click", function() {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            if (startDate && endDate) {
                window.location.href = "export_in.php?start_date=" + startDate + "&end_date=" + endDate;
            } else {
                alert("Please select both start and end date");
            }
        });
    });
</script>
</body>
</html>