<?php
require "authMiddleware.php";

if ($user["role"] == 'teknisi' && $user['username'] != 'gilang' && $user['username'] != 'Aries Nugraha') {
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
    (SELECT username FROM `user` WHERE id = ambil_barang.validator) AS validator_username
FROM
    ambil_barang ORDER BY ambil_barang.id DESC";
$result = $conn->query($sql);

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: url("assets/images/bg-validation.png");
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
    </div>
    <div style="margin:95px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
        <table
                class="table table-bordered table-dark border-white"
                id="item_table"
        >
            <thead>
            <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                <th class="text-warning" style="min-width: 100px">No</th>
                <th class="text-warning" style="min-width: 100px">Teknisi</th>
                <th class="text-warning" style="min-width: 100px">Uraian</th>
                <th class="text-warning" style="min-width: 100px">Tgl Permintaan</th>
                <th class="text-warning" style="min-width: 100px">Tgl Validasi</th>
                <th class="text-warning" style="min-width: 100px">Keperluan</th>
                <th class="text-warning" style="min-width: 100px">Validasi</th>
                <th class="text-warning" style="min-width: 100px">Aksi</th>
                <th class="text-warning" style="min-width: 100px">Validator</th>

            </tr>
            </thead>
            <tbody class="text-center fw-semibold">
            <?php if ($result->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td><?php echo $no++; ?></td>
                        <td><?php echo $row['teknisi_id']; ?></td>
                        <td>
                            <?php
                            $ambilBarangId = $row["id"];
                            $sqlBarang = "SELECT
                                stock.nama_barang,
                                ambil_barang_item.jumlah, 
                                ambil_barang_item.ambil_barang_id
                            FROM
                                ambil_barang_item
                                INNER JOIN
                                stock
                                ON ambil_barang_item.barang_id = stock.id
                            WHERE
                                ambil_barang_item.ambil_barang_id = $ambilBarangId";
                            $resultBarang = $conn->query($sqlBarang);

                            if (!$resultBarang) {
                                error_log("Error executing query for ambil_barang_id $ambilBarangId: " . $conn->error);
                            }

                            $namaBarangList = [];
                            $counter = 1;
                            while ($b = $resultBarang->fetch_assoc()) {
                                $namaBarangList[] = "{$counter}. " . $b['nama_barang']. " (".$b['jumlah'].") ";
                                $counter++;
                            }
                            echo implode(", ", $namaBarangList);
                            ?>
                            <a href="#" data-barang='<?php echo json_encode($namaBarangList); ?>' class="text-info" data-action="show-barang"></a>
                        </td>
                        <td><?php echo $row['tgl_permintaan']; ?></td>
                        <td><?php echo $row['tgl_validasi'] ?? '-'; ?></td>
                        <td><?php echo $row['keperluan'] ?? '-'; ?></td>
                        <td>
                            <?php if ($user["role"] == "supervisor" || $user['username'] == 'gilang'): ?>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="validasiAction.php?ambil_barang_id=<?php echo $row['id']; ?>" class="btn-request" onclick="return confirm('Apakah anda yakin ingin menvalidasi pengambilan barang tersebut?');">Validasi</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-outline-warning mb-2" href="hapusSPB.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah anda yakin ingin menghapus data tersebut?');" style="line-height: 0.5em">CancelSPB</a>
                        </td>
                        <td><?php echo $row['validator_username'] ?? '-'; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="daftar_barang_modal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="form_barang_modal_title">Daftar Barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-dark border-white">
                    <thead>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-warning">Nama Barang</th>
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

<script>
    $(document).ready(function () {
        $('#item_table').DataTable();

        $("[data-action='show-barang']").click(function () {
            let modalBarang = $("#daftar_barang_modal");
            let tableBarang = $("#daftar_barang_table");
            let barang = $(this).data('barang');
            console.log(barang);  // Debugging

            tableBarang.html("");

            barang.forEach((b, index) => {
                tableBarang.append(`<tr>
                    <td>${index + 1}. ${b}</td>
                </tr>`);
            });

            modalBarang.modal("show");
        });
    });
</script>
</body>
</html>
