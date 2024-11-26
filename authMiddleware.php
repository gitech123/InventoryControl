<?php
require "conn.php";
session_start();

$user = [];
if (isset($_SESSION["loggedin"])) {
    $userResult = $conn->query("SELECT * FROM user where id = " . $_SESSION['id']);

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
    }
    else {
        header("location: login.php");
    }
}
else {
    header("location: login.php");
}