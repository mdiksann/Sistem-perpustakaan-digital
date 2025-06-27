<?php
// admin/index.php
include_once '../includes/header.php';
include_once '../includes/sidebar_admin.php';
include_once '../config/koneksi.php';

// Proteksi agar hanya admin yang bisa mengakses
if ($_SESSION['level'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Statistik sederhana
$total_anggota = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pengguna WHERE level = 'user'"))['total'];
$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM buku"))['total'];
$buku_dipinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE status_pinjam = 'dipinjam'"))['total'];
?>

<div class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Anggota</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $total_anggota; ?></p>
            </div>
            <div class="text-blue-500 text-4xl">
                <i class="fas fa-users"></i> </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Buku</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $total_buku; ?></p>
            </div>
            <div class="text-green-500 text-4xl">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Buku Sedang Dipinjam</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $buku_dipinjam; ?></p>
            </div>
            <div class="text-yellow-500 text-4xl">
                <i class="fas fa-handshake"></i>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold mb-4">Informasi Penting</h3>
        <ul class="list-disc list-inside text-gray-700">
            <li>Gunakan menu di samping untuk mengelola sistem perpustakaan.</li>
            <li>Pastikan data buku dan anggota selalu terbaru.</li>
            <li>Pantau laporan peminjaman untuk manajemen yang lebih baik.</li>
        </ul>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>