<?php
require "authMiddleware.php";

if ($user['role'] != 'supervisor' && $user['role'] != 'supervisorWarehouse' && $user['username'] != 'gilang') {
    header("location: content.php");
}

$sql    = "SELECT * FROM user";
$result = $conn->query($sql);
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <style>
        body {
            background: url("assets/images/bg-user.png");
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
        <button class="button-primary btn-sm me-3" onclick="add()">Tambah User</button>
    </div>
    <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
        <table
                class="table table-bordered table-dark border-white"
                id="item_table"
        >
            <thead>
            <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                <th class="text-warning" style="min-width: 100px">Username</th>
                <th class="text-warning" style="min-width: 100px">NIK</th>
                <th class="text-warning" style="min-width: 100px">Area</th>
                <th class="text-warning" style="min-width: 100px">Role</th>
                <th class="text-warning" style="min-width: 100px">Aksi</th>
            </tr>
            </thead>
            <tbody class="text-center fw-semibold">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['nik']; ?></td>
                        <td><?php echo $row['area']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <div class="d-flex justify-content-center w-100">
                                <button class="btn btn-outline-warning me-3" style="line-height: 0.5em" onclick='showEdit(<?php echo json_encode($row) ?>)'>Edit
                                </button>

                                <a class="btn btn-outline-warning me-3" href="hapusBarang.php?id=<?php echo $row["id"] ?>" onclick="
                return confirm('Apakah anda yakin ingin menghapus data tersebut?');" style="line-height: 0.5em">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="form_user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="tambaEditUser.php" method="POST" id="form_user">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="form_user_modal_title"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-4 mb-2">
                            <label class="fw-semibold mb-1">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-4 mb-2" id="input_password">
                            <label class="fw-semibold mb-1">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="fw-semibold mb-1">NIK</label>
                            <input type="text" name="nik" class="form-control" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="fw-semibold mb-1">Area</label>
                            <input type="text" name="area" class="form-control" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="fw-semibold mb-1">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="teknisi">Teknisi</option>
                                <option value="adminGudang">Admin Warehouse</option>
                                <option value="adminMTC">Admin MTC</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="supervisorWarehouse">Supervisor Warehouse</option>
                            </select>
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

<script>
    function showEdit(data) {

        var modalEl = $("#form_user_modal");
        $("#form_user_modal_title").text("Edit User");
        $("#input_password").show();

        for (let key of Object.keys(data)) {
            if (key === 'role') {
                $(`select[name='role']`).val(data[key]).trigger("change");
            } else {
                $(`input[name='${key}']`).val(data[key]);
            }

        }

        modalEl.modal('show');
    }

    function add() {
        resetForm();
        var modalEl = $("#form_user_modal");
        $("#input_password").show();


        $("#form_user_modal_title").text("Tambah User Baru");
        modalEl.modal('show');
    }

    function resetForm() {
        $(`input[name='id']`).val("");
        $(`input[name='username']`).val("");
        $(`input[name='password']`).val("");
        $(`input[name='nik']`).val("");
        $(`input[name='area']`).val("");
        $(`select[name='role']`).val("").trigger("change");
    }

    $(document).ready(function () {
        $('#item_table').DataTable();
    });
</script>
</body>
</html>