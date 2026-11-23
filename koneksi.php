<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_tiket_kolam_renang";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>