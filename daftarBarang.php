<?php
require "authMiddleware.php";

if ($user['role'] == "supervisor") {
    header("location: content.php");
    exit();
}

if ($user['role'] == "teknisi") {
    $sqlSelect = "SELECT * FROM stock WHERE jumlah > 0";
} else {
    $sqlSelect = "SELECT * FROM stock";
}

$result = $conn->query($sqlSelect);

$cekkeranjang = "SELECT * FROM simpan_id_sementara";
$datakeranjang = $conn->query($cekkeranjang);

// Mengakhiri output buffering dan mengirimkan output setelah query selesai

?>

<html>

<head>
    <meta name="viewport" content="width=auto, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <style>
    body {
        background: url("assets/images/bg-barang.png");
        background-repeat: no-repeat;
        background-size: 100% 100%;
        background-position: center center;
    }

    .dt-search label {
        color: #fff;
    }

    td {
        align-content: center;
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

    <div class="loader" id="loader"></div> <!-- Loader untuk menunggu data -->
    <div class="container-fluid" id="main-content" style="display: none">
        <div class="toolbar mb-3">
            <a href="content.php">
                <img src="assets/images/menu-icon.png" alt="" class="menu-icon me-3">
            </a>
            <?php if ($user["role"] == "admin" || $user["role"] == 'superuser' || $user['role'] == 'supervisorWarehouse'): ?>
            <button class="button-primary btn-sm me-3" onclick="add()">Tambah Barang</button>
            <?php endif ?>

            <button class="button-primary btn-sm" id="btn_tambah_keranjang">Masukan Keranjang</button>
            <button class="button-primary btn-sm" id="btn_hapus_keranjang">Kosongkan Keranjang</button>
            <button class="button-primary btn-sm" id="btn_ambil_barang">Submit SPB</button>
        </div>
        <div style="margin:100px 100px 0 75px;height: auto;overflow-y: auto; overflow-x: auto" class="px-3">
            <table class="table table-bordered table-dark border-white" id="item_table">
                <thead>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-warning">No</th>
                        <?php if ($user["role"] == 'teknisi' || $user["role"] == 'superuser' || $user['role'] == 'supervisorWarehouse'): ?>
                        <th class="text-warning">Ambil</th>
                        <?php endif; ?>
                        <th class="text-warning">Area Penyimpanan</th>
                        <th class="text-warning">Kode Item</th>
                        <th class="text-warning">Kode Lokasi</th>
                        <th class="text-warning">Kategori</th>
                        <!-- <th class="text-warning">Tanggal</th> -->
                        <th class="text-warning">Nama Barang</th>
                        <th class="text-warning">Jumlah</th>
                        <th class="text-warning">Satuan</th>
                        <th class="text-warning">Peruntukan</th>
                        <?php if ($user["role"] == "admin" || $user["role"] == 'superuser' || $user['role'] == 'supervisorWarehouse'): ?>
                        <th class="text-warning">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="text-center fw-semibold">
                    <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <?php if ($user["role"] == 'teknisi' || $user["role"] == 'superuser' || $user['role'] == 'supervisorWarehouse'): ?>
                        <td>
                            <input class="form-check-input" name="ambil[]" type="checkbox"
                                value="<?php echo $row['id'] ?>">
                        </td>
                        <?php endif; ?>
                        <td><?php echo $row['area_penyimpanan']; ?></td>
                        <td><?php echo $row['kode_item']; ?></td>
                        <td><?php echo $row['kode_lokasi']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <!-- <td><//?php //echo $row['tanggal']; ?></td> -->
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td><?php echo $row['satuan']; ?></td>
                        <td><?php echo $row['peruntukan']; ?></td>
                        <?php if ($user["role"] == "admin" || $user["role"] == 'superuser' || $user['role'] == 'supervisorWarehouse'): ?>
                        <td class=" text-start">
                            <div class="d-flex">

                                <button class="btn btn-outline-warning mb-2" style="line-height: 0.5em"
                                    onclick='showEdit(<?php echo json_encode($row) ?>)'>Edit&nbsp;Stok
                                </button>&nbsp

                                <a class="btn btn-outline-danger mb-2"
                                    href="hapusBarang.php?id=<?php echo $row["id"] ?>" onclick="
                return confirm('Apakah anda yakin ingin menghapus data tersebut?');"
                                    style="line-height: 0.5em">Hapus</a>
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
    <div class="modal fade" id="form_barang_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="tambahEditBarang.php" method="POST" id="form_barang">
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
                                <label class="fw-semibold mb-1">Kode Item</label>
                                <input type="text" id="tambah_kode_item" name="kode_item"
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
                                <label class="fw-semibold mb-1">PO</label>
                                <input type="text" id="tambah_po" name="po" class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Supplier</label>
                                <input type="text" id="tambah_supplier" name="supplier"
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
                                <label class="fw-semibold mb-1">Peruntukan</label>
                                <input type="text" id="tambah_peruntukan" name="peruntukan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Status Persamaan Nama</label>
                                <input type="text" id="tambah_status_persamaan_nama" name="status_persamaan_nama"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="fw-semibold mb-1">Nama Oracle</label>
                                <input type="text" id="tambah_nama_oracle" name="nama_oracle"
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

    <div class="modal fade" id="stock_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="tambahStock.php" method="POST" id="form_stock">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="form_barang_modal_title">Tambah Stock</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                                <label class="fw-semibold mb-1">Jumlah Masuk</label>
                                <input type="text" name="jumlah" class="form-control form-control-sm">
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
    var btnAmbilBarang = $("#btn_ambil_barang");
    btnAmbilBarang.hide();
    var btnkeranjang = $("#btn_tambah_keranjang");
    btnkeranjang.hide();

    function showEdit(data) {

        var modalEl = $("#form_barang_modal");
        $(`input[name='barang_id']`).val(data['id']);
        $(`textarea[name='nama_barang']`).val(data['nama_barang']);
        $(`input[name='jumlah_awal']`).val(data['jumlah']);
        $(`input[name='kategori']`).val(data['kategori']);
        $(`input[name='satuan']`).val(data['satuan']);
        $("#form_barang_modal_title").text("Edit Barang");

        for (let key of Object.keys(data)) {
            $(`input[name='${key}']`).val(data[key]);
        }

        modalEl.modal('show');
    }

    function add() {
        resetForm();
        var modalEl = $("#form_barang_modal");
        $("#form_barang_modal_title").text("Tambah Barang");
        modalEl.modal('show');
    }

    function resetForm() {
        $(`input[name='id']`).val("");
        $(`input[name='area_penyimpanan']`).val("");
        $(`input[name='kode_lokasi']`).val("");
        $(`input[name='kategory']`).val("");
        $(`input[name='tanggal']`).val("");
        $(`input[name='po']`).val("");
        $(`input[name='supplier']`).val("");
        $(`input[name='nama_bararang']`).val("");
        $(`input[name='jumlah']`).val("");
        $(`input[name='satuan']`).val("");
        $(`input[name='peruntukan']`).val("");
        $(`input[name='status_persamaan_nama']`).val("");
        $(`input[name='nama_oracle']`).val("");
    }

    function showStock(data) {
        var modalEl = $("#stock_modal");

        $(`input[name='barang_id']`).val(data['id']);
        $(`textarea[name='nama_barang']`).val(data['nama_barang']);
        $(`input[name='jumlah_awal']`).val(data['jumlah']);

        modalEl.modal('show');
    }

    $(document).ready(function() {
        $("#loader").hide();
        $("#main-content").show();
        var dataTable = $('#item_table').DataTable();

        dataTable.on('draw', function() {
            $("input[type='checkbox']").change(function() {

                let checkedCount = $('input[type="checkbox"]:checked').length;

                if (checkedCount > 0) {
                    btnkeranjang.show();
                } else {
                    btnAmbilBarang.hide();
                    btnkeranjang.hide();
                }

                //if (checkedCount >= 3) {
                //    $('input[type="checkbox"]').not(':checked').prop('disabled', true);
                //} else {
                //    $('input[type="checkbox"]').prop('disabled', false);
                //}
            })
        })

        $("input[type='checkbox']").change(function() {
            let checkedCount = $('input[type="checkbox"]:checked').length;
            // console.log(checkedCount);
            let checkkategori = $('input[type="text"')
            if (checkedCount > 0) {
                btnkeranjang.show();
            } else {
                btnAmbilBarang.hide();
                btnkeranjang.hide();
            }

        })
        $('#btn_tambah_keranjang').click(function() {
            let selectedOptions = [];
            btnAmbilBarang.show();
            $('input[type="checkbox"]:checked').each(function() {
                selectedOptions.push($(this).val());
            });
            let databarang = selectedOptions.join(',');
            $.ajax({
                url: 'save_data.php',
                type: 'POST',
                data: {
                    databarang: databarang
                },
                success: function(response) {
                    alert('Data saved successfully');
                },
                error: function(error) {
                    alert('Error saving data: ' + error.responseText);
                }
            });
        })

        $('#btn_ambil_barang').click(function() {
            let selectedOptions = [];

            $('input[type="checkbox"]:checked').each(function() {
                selectedOptions.push($(this).val());
            });

            let barangIds = selectedOptions.join(',');

            document.location = `ambil.php?barangIds=${barangIds}`
        })

        $('#btn_hapus_keranjang').click(function() {
            let selectedOptions = [];
            btnAmbilBarang.hide();
            $('input[type="checkbox"]:checked').each(function() {
                selectedOptions.push($(this).val());
            });

            let barangIds = selectedOptions.join(',');

            if (confirm('Are you sure you want to delete the selected items?')) {
                // AJAX request to send data to server
                $.ajax({
                    url: 'hapus.php',
                    type: 'POST',
                    data: {
                        barangIds: barangIds
                    },
                    success: function(response) {
                        alert('Items deleted successfully');
                        // Optionally refresh the page or update the UI
                        location.reload();
                    },
                    error: function(error) {
                        alert('Error deleting items: ' + error.responseText);
                    }
                });
            }
        });
        $("input[name='tanggal']").flatpickr({
            dateFormat: "Y-m-d",
            enableTime: true,
            time_24hr: true,
            defaultDate: new Date(),
            clickOpens: true
        });
    });
    </script>
</body>

</html>