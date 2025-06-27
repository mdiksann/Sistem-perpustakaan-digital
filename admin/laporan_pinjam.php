<?php
// admin/laporan_pinjam.php
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

// Ambil semua data peminjaman
$query_laporan = "SELECT p.*, b.judul, b.gambar_buku, u.nama_lengkap, u.username
                  FROM peminjaman p
                  JOIN buku b ON p.id_buku = b.id_buku
                  JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                  ORDER BY p.tanggal_pinjam DESC";
$result_laporan = mysqli_query($koneksi, $query_laporan);
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Laporan Peminjaman Buku</h2>

    <?php if ($pesan_notifikasi): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $pesan_notifikasi; ?></span>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result_laporan) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">No.</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Judul Buku</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Peminjam</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Username</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tgl Pinjam</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tgl Estimasi Kembali</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tgl Aktual Kembali</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = 1; while ($laporan = mysqli_fetch_assoc($result_laporan)): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo $no++; ?></td>
                            <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($laporan['judul']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($laporan['nama_lengkap']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($laporan['username']); ?></td>
                            <td class="py-3 px-4"><?php echo date('d-m-Y', strtotime($laporan['tanggal_pinjam'])); ?></td>
                            <td class="py-3 px-4"><?php echo date('d-m-Y', strtotime($laporan['tanggal_kembali_estimasi'])); ?></td>
                            <td class="py-3 px-4">
                                <?php echo $laporan['tanggal_kembali_aktual'] ? date('d-m-Y', strtotime($laporan['tanggal_kembali_aktual'])) : '-'; ?>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?php
                                    if ($laporan['status_pinjam'] == 'dipinjam') {
                                        echo 'bg-yellow-200 text-yellow-800';
                                    } elseif ($laporan['status_pinjam'] == 'dikembalikan') {
                                        echo 'bg-green-200 text-green-800';
                                    } elseif ($laporan['status_pinjam'] == 'terlambat') {
                                        echo 'bg-red-200 text-red-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst($laporan['status_pinjam']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($laporan['status_pinjam'] == 'dipinjam'): ?>
                                    <a href="proses_admin.php?kembalikan_admin=<?php echo $laporan['id_peminjaman']; ?>"
                                       onclick="return confirm('Anda yakin ingin menandai buku ini sudah dikembalikan?')"
                                       class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Kembalikan (Admin)
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500 text-sm">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">Belum ada riwayat peminjaman.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>