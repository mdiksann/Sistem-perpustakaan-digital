<?php
// auth/proses_auth.php
session_start();
include_once '../config/koneksi.php';

// Fungsi Login
if (isset($_POST['login'])) {
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);

    $query = "SELECT * FROM pengguna WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $pengguna = mysqli_fetch_assoc($result);
        if (password_verify($password, $pengguna['password'])) {
            $_SESSION['id_pengguna'] = $pengguna['id_pengguna'];
            $_SESSION['username'] = $pengguna['username'];
            $_SESSION['nama_lengkap'] = $pengguna['nama_lengkap'];
            $_SESSION['level'] = $pengguna['level'];

            if ($pengguna['level'] == 'admin') {
                header('Location: ../admin/index.php');
            } else {
                header('Location: ../user/index.php');
            }
            exit();
        } else {
            $_SESSION['pesan_error_login'] = "Password salah!";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['pesan_error_login'] = "Username tidak ditemukan!";
        header('Location: login.php');
        exit();
    }
}

// Fungsi Signup
if (isset($_POST['signup'])) {
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $cek_username = "SELECT * FROM pengguna WHERE username = '$username'";
    $result_cek = mysqli_query($koneksi, $cek_username);

    if (mysqli_num_rows($result_cek) > 0) {
        $_SESSION['pesan_error_signup'] = "Username sudah terdaftar. Silakan gunakan username lain.";
        header('Location: signup.php');
        exit();
    }

    $query = "INSERT INTO pengguna (nama_lengkap, username, password, level) VALUES ('$nama_lengkap', '$username', '$password_hashed', 'user')";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['pesan_sukses_signup'] = "Akun berhasil dibuat! Silakan login.";
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['pesan_error_signup'] = "Gagal membuat akun: " . mysqli_error($koneksi);
        header('Location: signup.php');
        exit();
    }
}

// Fungsi Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>