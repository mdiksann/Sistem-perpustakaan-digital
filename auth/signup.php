<?php
// auth/signup.php
session_start();
include_once '../config/koneksi.php';

$pesan_sukses = '';
$pesan_error = '';
if (isset($_SESSION['pesan_sukses_signup'])) {
    $pesan_sukses = $_SESSION['pesan_sukses_signup'];
    unset($_SESSION['pesan_sukses_signup']);
}
if (isset($_SESSION['pesan_error_signup'])) {
    $pesan_error = $_SESSION['pesan_error_signup'];
    unset($_SESSION['pesan_error_signup']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Perpustakaan</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6">Daftar Akun Anggota</h2>
        <?php if ($pesan_sukses): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_sukses; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($pesan_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_error; ?></span>
            </div>
        <?php endif; ?>
        <form action="proses_auth.php" method="POST">
            <div class="mb-4">
                <label for="nama_lengkap" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" name="signup" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Daftar
                </button>
                <a href="login.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Sudah punya akun? Login!
                </a>
            </div>
        </form>
    </div>
</body>
</html>