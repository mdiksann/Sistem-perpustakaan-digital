<?php
// auth/login.php
session_start();
include_once '../config/koneksi.php';

if (isset($_SESSION['id_pengguna'])) {
    if ($_SESSION['level'] == 'admin') {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../user/index.php');
    }
    exit();
}

$pesan_error = '';
if (isset($_SESSION['pesan_error_login'])) {
    $pesan_error = $_SESSION['pesan_error_login'];
    unset($_SESSION['pesan_error_login']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <?php if ($pesan_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_error; ?></span>
            </div>
        <?php endif; ?>
        <form action="proses_auth.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
                <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" name="login" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Login
                </button>
                <a href="signup.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Belum punya akun? Daftar!
                </a>
            </div>
        </form>
    </div>
</body>
</html>