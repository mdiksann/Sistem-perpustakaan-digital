<?php
// user/riwayat_pinjam.php
include_once '../includes/header.php';
include_once '../includes/sidebar_user.php';
include_once '../config/koneksi.php';

$id_pengguna = $_SESSION['id_pengguna'];

$query_riwayat = "SELECT p.*, b.judul, b.gambar_buku
                  FROM peminjaman p
                  JOIN buku b ON p.id_buku = b.id_buku
                  WHERE p.id_pengguna = '$id_pengguna'
                  ORDER BY p.tanggal_pinjam DESC";
$result_riwayat = mysqli_query($koneksi, $query_riwayat);

$pesan_notifikasi = '';
if (isset($_SESSION['pesan_notifikasi_user'])) {
    $pesan_notifikasi = $_SESSION['pesan_notifikasi_user'];
    unset($_SESSION['pesan_notifikasi_user']);
}
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Riwayat Peminjaman & Pengembalian Buku</h2>

    <?php if ($pesan_notifikasi): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $pesan_notifikasi; ?></span>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result_riwayat) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">No.</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Gambar Buku</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Judul Buku</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tanggal Pinjam</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Estimasi Kembali</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tanggal Kembali Aktual</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Status</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = 1; while ($pinjam = mysqli_fetch_assoc($result_riwayat)): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo $no++; ?></td>
                            <td class="py-3 px-4">
                                <img src="../assets/images/<?php echo htmlspecialchars($pinjam['gambar_buku'] ?: 'no-image.jpg'); ?>" alt="<?php echo htmlspecialchars($pinjam['judul']); ?>" class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($pinjam['judul']); ?></td>
                            <td class="py-3 px-4"><?php echo date('d-m-Y', strtotime($pinjam['tanggal_pinjam'])); ?></td>
                            <td class="py-3 px-4"><?php echo date('d-m-Y', strtotime($pinjam['tanggal_kembali_estimasi'])); ?></td>
                            <td class="py-3 px-4">
                                <?php echo $pinjam['tanggal_kembali_aktual'] ? date('d-m-Y', strtotime($pinjam['tanggal_kembali_aktual'])) : '-'; ?>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?php
                                    if ($pinjam['status_pinjam'] == 'dipinjam') {
                                        echo 'bg-yellow-200 text-yellow-800';
                                    } elseif ($pinjam['status_pinjam'] == 'dikembalikan') {
                                        echo 'bg-green-200 text-green-800';
                                    } elseif ($pinjam['status_pinjam'] == 'terlambat') {
                                        echo 'bg-red-200 text-red-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst($pinjam['status_pinjam']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($pinjam['status_pinjam'] == 'dipinjam'): ?>
                                    <a href="proses_user.php?kembalikan=<?php echo $pinjam['id_peminjaman']; ?>" 
                                       onclick="return confirm('Anda yakin ingin mengembalikan buku ini?')"
                                       class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Kembalikan
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500 text-sm">Tidak ada aksi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">Anda belum memiliki riwayat peminjaman buku.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>