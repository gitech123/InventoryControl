<?php
require "authMiddleware.php";
require 'vendor/autoload.php';

$sql = "SELECT
    `Area Penyimpanan`,
    `Kode Lokasi`,
    Kategori,
    Tanggal,
    `Nama Barang`,
    Jml,
    Uom,
    `No SPB`,
    Bulan,
    Tahun
FROM
    barang_bekas_masuk
    ORDER BY id DESC
";

$result = $conn->query($sql);
?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <style>
    body {
        background: url("assets/images/bg-historyin-bekas.png");
        background-color: black;
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
            <a class="btn btn-primary" href="barang-bekas-keluar.php">Barang Keluar</a>
            <a class="btn btn-primary" href="barang-bekas.php">Dashboard</a>
            <input type="text" id="start_date" placeholder="Start Date" class="form-control d-inline w-auto">
            <input type="text" id="end_date" placeholder="End Date" class="form-control d-inline w-auto">
            <input type="text" id="item" placeholder="Item" class="form-control d-inline w-auto">
            <button id="exportBtn" class="btn btn-primary">Export</button>
        </div>
    </div>
    <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
        <table class="table table-bordered table-dark border-white" id="item_table">
            <thead>
                <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                    <th class="text-warning">No</th>
                    <th class="text-warning">Area Penyimpanan</th>
                    <th class="text-warning">Kode Lokasi</th>
                    <th class="text-warning">Tanggal Penerimaan</th>
                    <th class="text-warning">Kategori</th>
                    <th class="text-warning">Nama Barang</th>
                    <th class="text-warning">Jml</th>
                    <th class="text-warning">Uom</th>
                    <th class="text-warning">No SPB</th>
                    <th class="text-warning">Bulan</th>
                    <th class="text-warning">Tahun</th>
                </tr>
            </thead>
            <tbody class="text-center fw-semibold">
                <?php if ($result->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['Area Penyimpanan']; ?></td>
                    <td><?php echo $row['Kode Lokasi']; ?></td>
                    <td><?php echo $row['Tanggal']; ?></td>
                    <td><?php echo $row['Kategori']; ?></td>
                    <td><?php echo $row['Nama Barang']; ?></td>
                    <td><?php echo $row['Jml']; ?></td>
                    <td><?php echo $row['Uom']; ?></td>
                    <td><?php echo $row['No SPB']; ?></td>
                    <td><?php echo $row['Bulan']; ?></td>
                    <td><?php echo $row['Tahun']; ?></td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="assets/js/bootstrap.bundle.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="plugins/DataTables/datatables.min.js"></script>
    <script src="assets/js/flatpickr.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#item_table').DataTable();
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
                window.location.href = "export_bekas_masuk.php?start_date=" + startDate + "&end_date=" +
                    endDate + "&item=" + item;
            } else {
                alert("Please select both start and end date");
            }
        });
    });
    </script>
</body>

</html>