<?php
// includes/sidebar_user.php
?>
<div class="w-64 bg-blue-900 text-white min-h-screen p-4 flex flex-col">
    <div class="text-2xl font-bold mb-8 text-center">User Dashboard</div>
    <nav>
        <ul>
            <li class="mb-4">
                <a href="../user/index.php" class="block py-2 px-4 rounded hover:bg-blue-700">Daftar Buku</a>
            </li>
            <li class="mb-4">
                <a href="../user/riwayat_pinjam.php" class="block py-2 px-4 rounded hover:bg-blue-700">Riwayat Peminjaman</a>
            </li>
            <li class="mt-auto">
                <a href="../auth/proses_auth.php?logout=true" class="block py-2 px-4 rounded bg-red-600 hover:bg-red-700 text-center">Logout</a>
            </li>
        </ul>
    </nav>
</div>
<div class="flex-1 p-8"> <h1 class="text-3xl font-bold mb-6">Selamat datang di Perpustakaan, <?php echo $_SESSION['nama_lengkap']; ?>!</h1>