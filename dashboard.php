<?php
require "authMiddleware.php";

$sqlSelect = "SELECT * FROM stock ";
$showAll = isset($_GET['show_all']) ? $_GET['show_all'] : 0;

if (isset($_GET['filter']) && $_GET['filter'] == 'stock') {
    $sqlSelect .= " WHERE kategori = 'Stock'";
} elseif (isset($_GET['filter']) && $_GET['filter'] == 'all') {
    // Tidak perlu menambahkan WHERE jika ingin menampilkan semua barang
}

$result    = $conn->query($sqlSelect);

// Cek role user
$userRole = $_SESSION['role'];
?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/flatpickr.min.css">
    <style>
    body {
        background: url("assets/images/bg-dashboard.png");
        background-repeat: no-repeat;
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
<?php if (isset($_GET['success'])): ?>
<div id="successMessage" class="alert alert-success" role="alert">
    Safety stock berhasil diperbarui!
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div id="errorMessage" class="alert alert-danger" role="alert">
    Terjadi kesalahan saat memperbarui safety stock!
</div>
<?php endif; ?>
<body>
    <div class="loader" id="loader"></div>
    <div class="container-fluid" id="main-content" style="display: none">
        <!-- Tampilkan switch hanya untuk role tertentu -->
    <div class="toolbar mb-3">
        <a href="content.php">
            <img src="assets/images/menu-icon.png" alt="" class="menu-icon me-3">
        </a>
        <input type="text" id="start_date" placeholder="Start Date" class="form-control d-inline w-auto">
        <input type="text" id="end_date" placeholder="End Date" class="form-control d-inline w-auto">
        <select style="margin: 0px" class="form-control d-inline w-auto" id="kategori">
                                <option value="Non Stock">Non Stock</option>
                                <option value="Stock">Stock</option>
                            </select>
        <button id="exportBtn" class="btn btn-primary">Export</button>
        <?php if ($user['role'] === 'adminMTC' || $user['username'] == 'gilang'): ?>
            <form method="GET"  id="filterForm" action=""
                style="margin-left:10%; color:#fff; position:absolute; font-size:20px;">
                <label for="show_all" style="font-size:20px;">All Item</label>
                <input type="radio" name="filter" value="all" id="show_all"
                    <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'all') ? 'checked' : ''; ?> />

                <label for="show_stock" style="font-size:20px;">Stock</label>
                <input type="radio" name="filter" value="stock" id="show_stock"
                    <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'stock') ? 'checked' : ''; ?> />
            </form>

            <?php endif; ?>
    </div>
        <div style="margin:100px 100px 0 100px;height: 450px;overflow-y: auto; overflow-x: hidden">
            
            <table class="table table-bordered table-dark border-white" id="item_table">
                <thead>
                    <tr class="text-center fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-warning">Area Penyimpanan</th>
                        <th class="text-warning">Kode Lokasi</th>
                        <th class="text-warning">Kategori</th>
                        <th class="text-warning" style="width:40%;">Nama Barang</th>
                        <th class="text-warning">Satuan</th>
                        <th class="text-warning">Saldo Awal</th>
                        <th class="text-warning">Masuk</th>
                        <th class="text-warning">Keluar</th>
                        <th class="text-warning">Saldo Akhir</th>
                        <?php if (isset($_GET['filter']) && $_GET['filter'] == 'stock'): ?>
                        <th class="text-warning">Safety Stock</th> <!-- Kolom tambahan -->
                        <th class="text-warning">Indikator</th> <!-- Kolom baru untuk indikator -->
                        <?php endif; ?>

                    </tr>
                </thead>

                <tbody class="text-center fw-semibold">
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
    $barangId = $row['id'];
    $jumlah = $row['jumlah'];
    $safetyStock = $row['safety_stock'];

    // Tentukan kelas highlight berdasarkan kondisi
    $highlightClass = ($jumlah < $safetyStock) ? 'highlight' : 'highlight';
    if ($jumlah >= $safetyStock) {
        $indikator = 'Aman';
        $indikatorClass = 'indikator-aman'; // Kelas untuk aman
    } else {
        $indikator = 'Tidak Aman';
        $indikatorClass = 'indikator-tidak-aman'; // Kelas untuk tidak aman
    }
    ?>
                    <tr class="<?php echo $highlightClass; ?>">
                        <td><?php echo $row['area_penyimpanan']; ?></td>
                        <td><?php echo $row['kode_lokasi']; ?></td>
                        <td><?php echo $row['kategori']; ?></td>
                        <td style="width:40%;"><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['satuan']; ?></td>
                        <td>
                            <?php
            // Hitung saldo awal
            $jmlsaldoawal = 0;
            $sqlBarangMasuk = "SELECT * FROM barang_masuk where barang_id = $barangId";
            $resultBarangMasuk = $conn->query($sqlBarangMasuk);
            $jmlBarangMasuk = 0;
            while ($barangMasuk = $resultBarangMasuk->fetch_assoc()) {
                $jmlBarangMasuk += $barangMasuk["jumlah"];
            }
            $sqlBarangKeluar = "SELECT * FROM ambil_barang_item where barang_id = $barangId";
            $resultBarangKeluar = $conn->query($sqlBarangKeluar);
            $jmlBarangKeluar = 0;
            while ($barangKeluar = $resultBarangKeluar->fetch_assoc()) {
                $jmlBarangKeluar += $barangKeluar["jumlah"];
            }
            if($jmlBarangKeluar > 0 && $jmlBarangMasuk < 1) {
                $jmlsaldoawal = $row['jumlah'] + $jmlBarangKeluar;
            } else if($jmlBarangKeluar > 0 && $jmlBarangMasuk > 0) {
                $jmlsaldoawal = $row['jumlah'] + ($jmlBarangKeluar - $jmlBarangMasuk);
            } else if($jmlBarangKeluar < 1 && $jmlBarangMasuk > 0) {
                $jmlsaldoawal = $row['jumlah'] - $jmlBarangMasuk;
            } else {
                $jmlsaldoawal = $row['jumlah'];
            }
            echo $jmlsaldoawal;
            ?>
                        </td>
                        <td>
                            <?php
            // Tampilkan barang masuk
            echo $jmlBarangMasuk;
            ?>
                        </td>
                        <td>
                            <?php
            // Tampilkan barang keluar
            echo $jmlBarangKeluar;
            ?>
                        </td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <?php if (isset($_GET['filter']) && $_GET['filter'] == 'stock'): ?>
                        <td>
                            <form method="POST" action="edit_safety_stock.php" style="display: inline;">
                                <input type="hidden" name="barang_id" value="<?php echo $barangId; ?>">
                                <input type="number" style="width:50px;" name="safety_stock"
                                    value="<?php echo $row['safety_stock']; ?>" required>
                                <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                            </form>
                        </td>
                        <td>
                            <button
                                class="btn <?php echo ($jumlah >= $safetyStock) ? 'btn-success' : 'btn-danger'; ?> mb-2"
                                disabled>
                                <?php echo ($jumlah >= $safetyStock) ? 'Aman' : 'Tidak Aman'; ?>
                            </button>
                        </td>
                        <?php endif; ?>
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
    <script src="assets/js/flatpickr.min.js"></script>
    <script>
    document.querySelectorAll('input[name="filter"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            document.getElementById('filterForm').submit(); // Submit form otomatis saat opsi diubah
        });
    });
    flatpickr("#start_date", {dateFormat: "Y-m-d"});
    flatpickr("#end_date", {dateFormat: "Y-m-d"});

    $(document).ready(function() {
        $("#loader").hide();
        $("#main-content").show();
        $('#item_table').DataTable();
        $("#exportBtn").on("click", function() {
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
            var kategori = $("#kategori").val(); // Mengambil nilai dari kolom input "item"

            if (startDate && endDate) {
                window.location.href = "export.php?start_date=" + startDate + "&end_date=" + endDate + "&kategori=" + kategori;
            } else {
                alert("Please select both start and end date");
            }
        });
    });

    function hideMessage(messageId) {
        const message = document.getElementById(messageId);
        if (message) {
            setTimeout(() => {
                message.style.display = 'none';
            }, 5000); // 5000 ms = 5 detik
        }
    }

    // Panggil fungsi untuk menghilangkan pesan
    hideMessage('successMessage');
    hideMessage('errorMessage');
    </script>
</body>

</html>