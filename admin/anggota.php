<?php
// admin/anggota.php
include_once '../includes/header.php';
include_once '../includes/sidebar_admin.php';
include_once '../config/koneksi.php';

if ($_SESSION['level'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$pesan_notifikasi = '';
if (isset($_SESSION['pesan_notifikasi_admin'])) {
    $pesan_notifikasi = $_SESSION['pesan_notifikasi_admin'];
    unset($_SESSION['pesan_notifikasi_admin']);
}

// Ambil semua anggota (level 'user')
$query_anggota = "SELECT * FROM pengguna WHERE level = 'user' ORDER BY tanggal_daftar DESC";
$result_anggota = mysqli_query($koneksi, $query_anggota);
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Daftar Anggota Perpustakaan</h2>

    <?php if ($pesan_notifikasi): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $pesan_notifikasi; ?></span>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result_anggota) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">No.</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Nama Lengkap</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Username</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tanggal Daftar</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = 1; while ($anggota = mysqli_fetch_assoc($result_anggota)): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo $no++; ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($anggota['nama_lengkap']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($anggota['username']); ?></td>
                            <td class="py-3 px-4"><?php echo date('d-m-Y H:i', strtotime($anggota['tanggal_daftar'])); ?></td>
                            <td class="py-3 px-4">
                                <a href="proses_admin.php?hapus_anggota=<?php echo $anggota['id_pengguna']; ?>"
                                   onclick="return confirm('Anda yakin ingin menghapus anggota ini? Tindakan ini tidak dapat dibatalkan.')"
                                   class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    Hapus
                                </a>
                                </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">Belum ada anggota terdaftar.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>