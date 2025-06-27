<?php
// index.php
session_start();

if (isset($_SESSION['id_pengguna'])) {
    if ($_SESSION['level'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: user/index.php');
    }
    exit();
} else {
    header('Location: auth/login.php');
    exit();
}
?>