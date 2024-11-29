<?php
require "authMiddleware.php";

$sqlSelect = "SELECT * FROM mutasi_part_bekas ";
$result    = $conn->query($sqlSelect);

// Cek role user
$userRole = $_SESSION['role'];
?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <style>
    body {
        background: url("assets/images/bgbarbek.png");
        background-repeat: no-repeat;
        background-color: black;
        background-size: 100% 100%;
        background-position: center top;
    }

    .dt-search label {
        color: #fff;
    }

    .highlight {
        background-color: red !important;
        color: white !important;
    }


    td {
        align-content: center;
    }

    .indikator-aman {
        color: green;
        /* Warna untuk aman */
        font-weight: bold;
        /* Untuk menebalkan teks */
    }

    .indikator-tidak-aman {
        color: red;
        /* Warna untuk tidak aman */
        font-weight: bold;
        /* Untuk menebalkan teks */
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
    <div class="container-fluid" style="display: none" id="main-content">
        <div class="toolbar mb-3">
            <a href="content.php">
                <img src="assets/images/menu-icon.png" alt="" class="menu-icon me-3">
            </a>
            <a class="btn btn-primary" href="barang-bekas-masuk.php">Barang Masuk</a>
            <a class="btn btn-primary" href="barang-bekas-keluar.php">Barang Keluar</a>
            <button class="btn btn-primary" onclick="add()">Tambah Barang</button>
            <input type="text" id="item" placeholder="Item" class="form-control d-inline w-auto">
            <button id="exportBtn" class="btn btn-primary">Export</button>

        </div>

        <!-- Tampilkan switch hanya untuk role tertentu -->


        <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
            <table class="table table-bordered table-dark border-white" id="item_table">
                <thead>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-warning">Area Penyimpanan</th>
                        <th class="text-warning">Kode Lokasi</th>
                        <th class="text-warning">Kategori</th>
                        <th class="text-warning" style="width:30%;">Nama Barang</th>
                        <th class="text-warning">Satuan</th>
                        <th class="text-warning">Saldo Awal</th>
                        <th class="text-warning">Masuk</th>
                        <th class="text-warning">Keluar</th>
                        <th class="text-warning">Saldo Akhir</th>
                        <th class="text-warning">Keterangan</th>
                        <th class="text-warning">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-center fw-semibold">
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $barangId = $row['id']; ?>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <td><?php echo $row['Area Penyimpanan']; ?></td>
                        <td><?php echo $row['Kode_lokasi']; ?></td>
                        <td><?php echo $row['Kategori']; ?></td>
                        <td style="width:30%;"><?php echo $row['Nama_Barang']; ?></td>
                        <td><?php echo $row['Satuan']; ?></td>
                        <td><?php echo $row['Saldo_Awal']; ?></td>
                        <td><?php echo $row['Masuk']; ?></td>
                        <td><?php echo $row['Keluar']; ?></td>
                        <td><?php echo $row['Saldo_Akhir']; ?></td>
                        <td><?php echo $row['Keterangan_Potensi_Pemakaian_Barang']; ?></td>
                        <?php if ($user["role"] == "admin" || $user["role"] == 'superuser' || $user["role"] == "adminMTC" || $user['role'] == 'supervisorWarehouse' || $user['role'] == 'supervisor' ||  $user['username'] == 'gilang' ): ?>
                        <td class=" text-start">
                            <div class="d-flex">
                                <button class="btn btn-outline-warning mb-2" style="line-height: 0.5em"
                                    onclick='showEdit(<?php echo json_encode($row) ?>)'>Tambah&nbsp;Jumlah</button>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-primary mb-2" style="line-height: 0.5em"
                                    onclick='showEditOut(<?php echo json_encode($row) ?>)'>Keluarkan&nbsp;Barang</button>
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

    <div class="modal fade" id="form_barang_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="tambahEditBarangBekas.php" method="POST" id="form_barang">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="form_barang_modal_title">Tambah Barang</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Area Penyimpanan</label>
                                <input type="text" id="tambah_area_penyimpanan" name="area_penyimpanan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Kode Lokasi</label>
                                <input type="text" id="tambah_kode_lokasi" name="kode_lokasi"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Kategori</label>
                                <input type="text" id="tambah_kategori" name="kategori"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Tanggal</label>
                                <input type="text" id="tambah_tanggal" name="tanggal"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Nama Barang</label>
                                <input type="text" id="tambah_nama_barang" name="nama_barang"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Jumlah Awal</label>
                                <input type="text" name="jumlah_awal" class="form-control form-control-sm" disabled>
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Jumlah Masuk</label>
                                <input type="text" id="tambah_jumlah" name="jumlah_baru"
                                    class="form-control form-control-sm" value="0">
                            </div>

                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Satuan</label>
                                <input type="text" id="tambah_satuan" name="satuan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">No SPB</label>
                                <input type="text" id="tambah_no_spb" name="no_spb"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Keterangan Potensi Pemakaian Barang</label>
                                <input type="text" id="tambah_potensi_pemakaian" name="potensi_pemakaian"
                                    class="form-control form-control-sm">
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

    <div class="modal fade" id="form_barang_modal_keluar" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="KeluarBarangBekas.php" method="POST" id="form_barang_keluar">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="form_barang_modal_title">Keluarkan Barang</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Area Penyimpanan</label>
                                <input type="text" id="tambah_area_penyimpanan" name="area_penyimpanan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Kode Lokasi</label>
                                <input type="text" id="tambah_kode_lokasi" name="kode_lokasi"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Kategori</label>
                                <input type="text" id="tambah_kategori" name="kategori"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Tanggal</label>
                                <input type="text" id="tambah_tanggal" name="tanggal"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Nama Barang</label>
                                <input type="text" id="tambah_nama_barang" name="nama_barang"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Jumlah Awal</label>
                                <input type="text" name="jumlah_awal" class="form-control form-control-sm" disabled>
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Jumlah Keluar</label>
                                <input type="text" id="tambah_jumlah" name="jumlah_baru"
                                    class="form-control form-control-sm" value="0">
                            </div>

                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Satuan</label>
                                <input type="text" id="tambah_satuan" name="satuan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">No SPP</label>
                                <input type="text" id="tambah_no_spb" name="no_spb"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Peruntukan</label>
                                <input type="text" id="tambah_peruntukan" name="peruntukan"
                                    class="form-control form-control-sm">
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
        var modalEl = $("#form_barang_modal");
        // $(`input[name='id']`).val(data['id']); // Gunakan id untuk barang
        $(`input[name='nama_barang']`).val(data['Nama_Barang']); // Ganti ke input
        $(`input[name='kode_lokasi']`).val(data['Kode_lokasi']); // Ganti ke input
        $(`input[name='area_penyimpanan']`).val(data['Area Penyimpanan']); // Ganti ke input
        $(`input[name='jumlah_awal']`).val(data['Saldo_Akhir']); // Gunakan Saldo Akhir sebagai jumlah awal
        $(`input[name='kategori']`).val(data['Kategori']);
        $(`input[name='satuan']`).val(data['Satuan']);
        $(`input[name='no_spb']`).val(data['No_SPB']);
        $(`input[name='potensi_pemakaian']`).val(data['Keterangan_Potensi_Pemakaian_Barang']);
        $("#form_barang_modal_title").text("Edit Barang");

        modalEl.modal('show');
    }

    function showEditOut(data) {
        var modalEl = $("#form_barang_modal_keluar");
        // $(`input[name='id']`).val(data['id']); // Gunakan id untuk barang
        $(`input[name='nama_barang']`).val(data['Nama_Barang']); // Ganti ke input
        $(`input[name='kode_lokasi']`).val(data['Kode_lokasi']); // Ganti ke input
        $(`input[name='area_penyimpanan']`).val(data['Area Penyimpanan']); // Ganti ke input
        $(`input[name='jumlah_awal']`).val(data['Saldo_Akhir']); // Gunakan Saldo Akhir sebagai jumlah awal
        $(`input[name='kategori']`).val(data['Kategori']);
        $(`input[name='satuan']`).val(data['Satuan']);
        $(`input[name='no_spb']`).val(data['No_SPB']);
        $(`input[name='peruntukan']`).val("");
        $("#form_barang_modal_title").text("Keluarkan Barang");

        modalEl.modal('show');
    }

    function add() {
        resetForm();
        var modalEl = $("#form_barang_modal");
        $("#form_barang_modal_title").text("Tambah Barang");
        modalEl.modal('show');
    }

    function resetForm() {
        $(`input[name='id']`).val(""); // Gunakan id untuk barang
        $(`input[name='nama_barang']`).val(""); // Ganti ke input
        $(`input[name='kode_lokasi']`).val(""); // Ganti ke input
        $(`input[name='area_penyimpanan']`).val(""); // Ganti ke input
        $(`input[name='jumlah_awal']`).val(""); // Gunakan Saldo Akhir sebagai jumlah awal
        $(`input[name='kategori']`).val("");
        $(`input[name='satuan']`).val("");
        $(`input[name='no_spb']`).val("");
    }
    $(document).ready(function() {
        $("#loader").hide();
        $("#main-content").show();
        $('#item_table').DataTable(); //bootstrap untuk search
        flatpickr("#tambah_tanggal", {
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });
    });
    $("#exportBtn").on("click", function() {
        var item = $("#item").val(); // Mengambil nilai dari kolom input "item"
                window.location.href = "export_bekas.php?" + "item=" + item;
        });
    </script>
</body>

</html>