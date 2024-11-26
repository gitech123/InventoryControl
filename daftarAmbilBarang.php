<?php
require "authMiddleware.php";

$sql    = "SELECT
	ambil_barang.id,
	ambil_barang.teknisi_id,
	ambil_barang.barang_id,
	ambil_barang.status,
	stock.nama_barang,
	user.username
FROM
	`ambil_barang`
	LEFT JOIN `stock` ON stock.id = ambil_barang.barang_id
	LEFT JOIN `user` ON user.id = ambil_barang.teknisi_id";
$result = $conn->query($sql);

?>

<html>
<head>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/datatable/datatable.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container" style="height: 100%">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table
                        class="table table-bordered table-dark border-white"
                        id="item_table"
                >
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 gs-0">
                        <th>Nama Barang</th>
                        <th>Teknisi</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['nama_barang']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['status']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/datatable/datatable.min.js"></script>

<script>
    $(document).ready(function () {
        $('#item_table').DataTable();
    });
</script>
</body>
</html>