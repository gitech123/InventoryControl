<?php
require "conn.php";
session_start();

//if (isset($_SESSION["loggedin"])) {
//    header("location: index.php");
//}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id,nik,password FROM user WHERE nik = ?");
    $stmt->bind_param("s", $username);

    // Execute statement
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, so start a new session
            $_SESSION["loggedin"] = true;
            $_SESSION["id"]       = $id;
            header("location: content.php");
        }
        else {
            // Display an error message if password is not valid
            echo "<script>alert('password salah')</script>";
        }
    }
    else {
        // Jika Username tidak ditemukan, coba metode lain
        $stmt = $conn->prepare("SELECT id,username,password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);

        // Execute statement
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Bind result variables
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();
    
            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, so start a new session
                $_SESSION["loggedin"] = true;
                $_SESSION["id"]       = $id;
                header("location: content.php");
            }
            else {
                // Display an error message if password is not valid
                echo "<script>alert('password salah')</script>";
            }
        }
        else {
        echo "<script>alert('username tidak tersedia')</script>";
        }
        
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>


<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Inventory Control</title>
    <link rel="icon" href="assets/images/Logo_santos_jaya_abadi.png">
    <style>
        body {
            background: url("assets/images/bg-login.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-position: center top;
        }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="height: 100%">
    <div style="margin-top: -70px;width: 300px">
        <form action="#" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Nama atau NIK">
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <!-- Menggunakan <button> dengan gambar latar belakang -->
            <button type="submit" class="btn btn-success" style="background: none; border: none; padding: 0; margin: 0px 0px 0px 85px;">
                <img src="assets/images/tombol_login.png" alt="Login" style="width: 130px; height: 40px;">
            </button>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>