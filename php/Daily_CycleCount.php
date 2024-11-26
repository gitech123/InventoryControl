<?php
require 'authMiddleware.php';
require 'functions.php';
$sparepart=query("SELECT * FROM sparepart");

//Pencarian Data
if(isset($_POST["cari"])){
    $sparepart=cari($_POST["keyword"]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SparMan</title>
</head>
<!-- Bootstrap -->
<link rel="stylesheet" href="./css/bootstrap.min.css">
<link rel="stylesheet" href="./css/jquery.dataTables.min.css">
<link rel="stylesheet" href="./css/style.css">

<style>
body {
    background-image: url("./img/coffee3.jfif");
    background-size:cover;
    background-repeat:no-repeat;
}
h1 {
    font-weight: 900;
    margin: 10px 335px;
    border-radius: 15px;
    background: #ffffff26;
    -webkit-flex-basis: 38%;
    /* -webkit-box-shadow: -2px 7px 37px 8px rgb(0 0 0 / 52%); */
    -moz-box-shadow: -2px 7px 37px 8px rgba(0,0,0,0.52);
    box-shadow: -2px 7px 37px 8px rgb(0 0 0 / 52%);
    border-radius: 20px;
    filter: drop-shadow(2px 4px 6px black);
 
}
#example_filter > label > input{
    background: Azure;
    color:black;
}

.sorting {
    text-align: center !important;
}

#example_length > label > select {
    color: #000000 !important;
    text-shadow: 0px 0px 2px #19ffff;
    font-weight: 900;
    font-family: cursive;
    padding: 0;
    background-color: snow;
    margin-top: 5px;
}
#example_wrapper {
    background:hsl(0deg 0% 0% / 55%);
    filter: drop-shadow(aqua 2px 5px 15px);
}

/*POPUP OVERLAY Detail History*/
.detail {
    position: fixed;
    display:none;
    border:3px solid aqua;
    border-style:groove;
    margin:auto;
    width:25%;
    height: 45%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 2;
    cursor: pointer;
    padding:25px;
    background:linear-gradient(45deg,#bdc3c7,#2c3e50);
    border-radius:25px;

}

.col-input {
    padding: 0;
    display: flex;
    flex-direction: column;
}
.col-input > input {
    margin: 2.5px 0;
    border-radius: 10px;
    max-width: inherit;
    
}

.handle-popup-wrapper {
    display: flex;
    justify-content: space-around;
}

</style>

<body>
<div class="p-3 mb-2 text-white">
<h1>Data Spare Part Workshop</h1>
<a class="btn btn-sm btn-dark" href="sparman.php">Tampilkan Semua Data</a>
<!-- <a class="btn btn-sm btn-dark" onclick="admin()">Admin Mode</a> -->
<a class="btn btn-sm btn-dark" href="logout.php">Logout</a>
<br>
<br>
<br>
<table id="example" class="table table-bordered table-hover" style="background:#24272b; text-align:center">
    <thead>
        <tr style="Font-family:courier; color:yellow;">
            <th>No.</th>
            <th>Opsi</th>
            <!-- <th>ID</th> -->
            <th>Area</th>
            <th>Brand</th>
            <th>Type</th>
            <th>KW/HP</th>
            <th>Qty</th>
            <th>Lokasi</th>
            <th>Mounted</th>
            <th>Keterangan</th>        
        </tr>
    </thead>

    <tbody>
        <?php 
        $i="1";
        foreach($sparepart as $row) : ?>
        <tr>
            <td><?= $i?></td>
            <td>
                <a class="btn btn-sm btn-outline-warning" href="verify.php?id=<?= $row["id"]?>" onclick="
                return confirm('Apakah anda yakin item yang dipilih sudah sesuai?');" style="line-height: 0.5em">Ambil</a>
            </td>
            <!-- <td><?= $row["id"]?></td> -->
            <td><?= $row["area_mesin"]?></td>
            <td><?= $row["brand"]?></td>
            <td><?= $row["tipe"]?></td>
            <td><?= $row["capacity"]?></td>
            <td><?= $row["qty"]?></td>
            <td><?= $row["lokasi"]?></td>
            <td><?= $row["mounted"]?></td>
            <td><?= $row["keterangan"]?></td>
        </tr>
        <?php
        $i++;
        endforeach; ?>
    </tbody>
    <tfoot>
        
    </tfoot>
</table>

</body>
<script src="./js/jquery-3.5.1.js"></script>
<script type="text/javascript" src="./js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {

   var table = $('#example').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }

    } );

    $('#min, #max').on('change', function() {
        table.draw();
    });

} );

function admin() {
    let temp = prompt("Masukkan Password");
    if(temp == 212){
        alert("Admin Mode UNLOCKED!");
        document.location.href='index.php';
    }else{
        alert("Code Salah!");
        return false;
    }
}

</script>
</html>