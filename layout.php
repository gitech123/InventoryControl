<?php
require "authMiddleware.php";
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: black;
            /* background: url("assets/images/bg-dashboard.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-position: center top; */
        }

        .dt-search label {
            color: #fff;

        }
        .popup {
            position: fixed;
            display:none;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            cursor: pointer;
            flex-direction: column;
            flex-wrap: wrap;
            align-content: center;
            justify-content: center;
            background-color: #000000ab;
        }
        #btn-petunjuk, #btn-menu {
            position: relative;
            left: 3rem;
            top: 6.5rem;
            width: 10rem;
        }

    </style>
</head>
<body>
<div class="container-fluid">
    <a class="btn btn-sm btn-dark" id="btn-petunjuk" onclick="popupopen()">PETUNJUK</a>
    <a href="content.php" class="btn btn-sm btn-warning" id="btn-menu">MENU</a>
    <img style="width: 100%; padding: 10px;" src="assets/images/layout_baru_240924.jpg" alt="">
</div>
<div class="popup" id="popup" onclick="popupclose()">
        <img src="assets/images/Hirarki_Kode_Lokasi.png" alt="">
        <a class="btn btn-sm btn-danger" onclick="popupclose()" style="width: 5%; align-self: center;margin-top: 1rem;">CLOSE</a>
</div>

<script src="assets/js/bootstrap.bundle.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="plugins/DataTables/datatables.min.js"></script>

<script>
    function popupopen() {
        document.getElementById("popup").style.display = "flex";
    }

function popupclose() {
        document.getElementById("popup").style.display = "none";
    }
</script>
</body>
</html>