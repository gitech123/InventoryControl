<?php
require "authMiddleware.php";

$userId = $user["id"];

if ($user["role"] != 'teknisi') {
    header("location: content.php");
}

$sql = "SELECT
	ambil_barang.id, 
	ambil_barang.teknisi_id, 
	ambil_barang.`no`, 
	ambil_barang.tgl_permintaan, 
	ambil_barang.keperluan, 
	ambil_barang.tgl_dibutuhkan, 
	ambil_barang.dikirim_ke, 
	ambil_barang.`status`, 
	ambil_barang.`tgl_validasi`, 
	stock.nama_barang, 
	ambil_barang_item.satuan, 
	ambil_barang_item.jumlah
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
		";

$result = $conn->query($sql);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: url("assets/images/bg-history.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
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
    </div>
    <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
        <table
                class="table table-bordered table-dark border-white"
                id="item_table"
        >
            <thead>
            <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                <th class="text-warning" style="min-width: 100px">No</th>
                <th class="text-warning" style="min-width: 100px">Nama Barang</th>
                <th class="text-warning" style="min-width: 100px">Sat</th>
                <th class="text-warning" style="min-width: 100px">Jml</th>
                <th class="text-warning" style="min-width: 100px">Teknisi</th>
                <th class="text-warning" style="min-width: 100px">Tgl Permintaan</th>
                <th class="text-warning" style="min-width: 100px">Tgl Validasi</th>
                <th class="text-warning" style="min-width: 100px">Status</th>
            </tr>
            </thead>
            <tbody class="text-center fw-semibold">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['no']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['satuan']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td><?php echo $row['teknisi_id']; ?></td>
                        <td><?php echo $row['tgl_permintaan']; ?></td>
                        <td><?php echo $row['tgl_validasi'] ?? '-'; ?></td>
                        <td><?php echo $row['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="plugins/DataTables/datatables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#item_table').DataTable();
    });
</script>
</body>
</html>