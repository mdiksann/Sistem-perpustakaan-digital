<?php
// config/koneksi.php

$host     = "localhost";
$user     = "root"; 
$password = "";     
$database = "perpustakaan_db"; 

$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (mysqli_connect_errno()) {
    echo "Gagal terhubung ke MySQL: " . mysqli_connect_error();
    exit();
}

// Set timezone untuk fungsi tanggal
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}
?>