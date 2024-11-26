<?php
require "authMiddleware.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = $_POST["id"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $nik      = $_POST["nik"];
    $area     = $_POST["area"];
    $role     = $_POST["role"];


    if ($id == '') {
        $sqlAction = "INSERT INTO user VALUES (
                           '',
                           '$username',
                           '$password',
                           '$role',
                           '$area',
                           '$nik'
                           )";

        if ($conn->query($sqlAction)) {
            echo "<script> alert('tambah user berhasil');document.location = 'daftarUser.php'; </script>";
        }
    }
    else {
        $sqlAction = "UPDATE user SET 
                  username = '$username',
                  password = '$password',
                  nik = '$nik',
                  area = '$area',
                  role = '$role'
            WHERE id = $id
                  ";

        if ($conn->query($sqlAction)) {
            echo "<script> alert('edit user berhasil');document.location = 'daftarUser.php' </script>";
        }
    }
}
?>