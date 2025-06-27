<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - <?php echo ucfirst($_SESSION['level']); ?></title>
    <link href="/perpustakaan_digital/css/style.css" rel="stylesheet">
    </head>
<body class="bg-gray-100 flex">