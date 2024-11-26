<?php
require "authMiddleware.php"; // Memastikan autentikasi pengguna

// Menghubungkan ke database
$sqlSelect = "SELECT * FROM stock WHERE kategori = 'Stock'";
$result = $conn->query($sqlSelect);

// Cek kesalahan dalam query
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>
<html>

<head>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <style>
    body {
        <?php if($user["role"]=="admin"|| $user["role"]=="adminMTC"): ?> background: url("assets/images/Page_Admin.png");
        <?php elseif($user["role"]=="teknisi"&& $user['username'] !='gilang'): ?> background: url("assets/images/Page_Teknisi.png");
        <?php else: ?> background: url("assets/images/bg-content.png");
        <?php endif;
        ?>background-repeat: no-repeat;
        background-size: 100% 100%;
        background-position: center top;
        color: #ffffff;
    }

    .menu-icon {
        width: 150px;
        height: 130px;
        transition: 0.5s;
    }

    .menu-icon1 {
        width: 150px;
        height: 130px;
        top: 50px;
        transition: 0.5s;
    }

    .menu-icon2 {
        width: 150px;
        height: 130px;
        top: 100px;
        transition: 0.5s;
    }

    .menu-icon:hover {
        transform: translateY(10px);
        box-shadow: 0 0 3px 5px rgb(101 255 255);
    }

    .menu-icon2:hover {
        transform: translateY(10px);
        box-shadow: 0 0 3px 5px rgb(101 255 255);
    }

    .menu-icon1:hover {
        transform: translateY(10px);
        box-shadow: 0 0 3px 5px rgb(101 255 255);
    }

    .menu-link {
        margin-right: 20px;
        margin-left: 20px;
    }

    .running-text {
        position: relative;
        text-align: center;
        margin: 0px 0;
    }
    </style>
</head>

<body>
    <?php if ($user['role'] == 'supervisorWarehouse' || $user["role"] == "adminMTC" || $user['role'] == 'admin' || $user['username'] == 'gilang'): ?>
        <div class="running-text">
        <?php
        // Cek apakah ada data yang tidak aman
        $unsafeItems = []; // Array untuk menyimpan nama barang yang tidak aman
        foreach ($result as $row) {
            if ($row['jumlah'] < $row['safety_stock']) {
                $unsafeItems[] = $row['nama_barang']; // Ganti 'nama_barang' dengan nama kolom yang sesuai di tabel
            }
        }
        if (!empty($unsafeItems) ) : ?>
            <marquee behavior="scroll" direction="left" style="color: red; font-size: 20px; background:white;">
               Peringatan: Ada barang yang tidak aman! Silakan periksa safety stock. Barang yang tidak aman: <?php echo implode("  ||||  ", $unsafeItems); ?>
            </marquee>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="container">
        <div style="position: relative;top:50px;">
            <span class="fw-bold"><i class="fs-3">Welcome <?php echo $user["username"] ?></i></span>
        </div>
        <div class="d-flex justify-content-end">
            <a href="logout.php">
                <img src="assets/images/btn-logout.png" alt="" style="padding: 10rem 0;">
            </a>
        </div>
        <div class="d-flex justify-content-center align-items-center">
            <div class="d-flex">
                <?php if ($user['role'] == 'supervisorWarehouse' || $user["role"] == "adminMTC" || $user['role'] == 'admin' && $user['username'] != 'gilang' && $user['username'] != 'Aries Nugraha'): ?>
                <a href="dashboard.php" class="menu-link">
                    <img src="assets/images/dashboard-icon.png" alt="" class="menu-icon">
                </a>
                <a href="daftarBarang.php" class="menu-link">
                    <img src="assets/images/barang-icon.png" alt="" class="menu-icon">
                </a>
                <a href="historyStock.php" class="menu-link">
                    <img src="assets/images/historyIN.png" alt="" class="menu-icon">
                </a>
                <a href="historyOUT.php" class="menu-link">
                    <img src="assets/images/historyOUT.png" alt="" class="menu-icon">
                </a>
                <a href="ambilValidasi.php" class="menu-link">
                    <img src="assets/images/validation-icon.png" alt="" class="menu-icon">
                </a>
                <a href="layout.php" class="menu-link">
                    <img src="assets/images/Layout.png" alt="" class="menu-icon">
                </a>
                <a href="barang-bekas.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/barang-bekas.png" style="margin:0;" alt="" class="menu-icon2">
                </a>

                
                
                <?php endif; ?>

                <?php if ($user['role'] == 'teknisi' && $user['username'] != 'gilang' && $user['username'] != 'Aries Nugraha'): ?>
                <a href="layout.php" class="menu-link">
                    <img src="assets/images/Layout.png" alt="" class="menu-icon">
                </a>
                <a href="daftarBarang.php" class="menu-link">
                    <img src="assets/images/barang-icon.png" alt="" class="menu-icon">
                </a>
                <a href="historyAmbilBarang.php" class="menu-link">
                    <img src="assets/images/history-icon.png" alt="" class="menu-icon">
                </a>
                <?php endif; ?>

                <?php if ($user['role'] == 'supervisor' && $user['username'] != 'gilang' && $user['username'] != 'Aries Nugraha'): ?>

                <a href="dashboard.php" class="menu-link">
                    <img src="assets/images/dashboard-icon.png" alt="" class="menu-icon">
                </a>
                <a href="daftarUser.php" class="menu-link">
                    <img src="assets/images/user-icon.png" alt="" class="menu-icon">
                </a>
                <a href="historyStock.php" class="menu-link">
                    <img src="assets/images/historyIN.png" alt="" class="menu-icon">
                </a>
                <a href="historyOUT.php" class="menu-link">
                    <img src="assets/images/historyOUT.png" alt="" class="menu-icon">
                </a>
                <a href="ambilValidasi.php" class="menu-link">
                    <img src="assets/images/validation-icon.png" alt="" class="menu-icon">
                </a>
                <a href="layout.php" class="menu-link">
                    <img src="assets/images/Layout.png" alt="" class="menu-icon">
                </a>
                <a href="barang-bekas.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/barang-bekas.png" style="margin:0;" alt="" class="menu-icon2">
                </a>
                <?php endif; ?>

                <?php if ( $user['username'] == 'Aries Nugraha' || $user["role"] == 'superuser'): ?>
                <a href="daftarBarang.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/barang-icon.png" style="margin:0;" alt="" class="menu-icon1">
                </a>
                <a href="dashboard.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/dashboard-icon.png" style="margin:0;" alt="" class="menu-icon1">
                </a>
                <a href="daftarUser.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/user-icon.png" style="margin:0;" alt="" class="menu-icon1">
                </a>
                <a href="historyStock.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/historyIN.png" style="margin:0;" alt="" class="menu-icon1">
                </a>
                <a href="historyOUT.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/historyOUT.png" style="margin:0;" alt="" class="menu-icon2">
                </a>
                <a href="ambilValidasi.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/validation-icon.png" style="margin:0;" alt="" class="menu-icon2">
                </a>
                <a href="layout.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/Layout.png" style="margin:0;" alt="" class="menu-icon2">
                </a>
                <a href="barang-bekas.php" style="margin:0;" class="menu-link">
                    <img src="assets/images/barang-bekas.png" style="margin:0;" alt="" class="menu-icon2">
                </a>
                <a href="dashboard-repairable.php" style="margin:2px;" class="menu-link">
                    <img src="assets/images/repairable-icon.png" style="margin:0;" alt="" class="menu-icon2">
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.js"></script>
</body>

</html>