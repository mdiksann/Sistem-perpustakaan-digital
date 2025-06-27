<?php
// admin/buku.php
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

// Ambil data buku untuk ditampilkan
$query_buku = "SELECT * FROM buku ORDER BY judul ASC";
$result_buku = mysqli_query($koneksi, $query_buku);

// Data buku untuk mode edit
$edit_buku = null;
if (isset($_GET['edit'])) {
    $id_buku_edit = (int)$_GET['edit'];
    $query_edit = "SELECT * FROM buku WHERE id_buku = '$id_buku_edit'";
    $result_edit = mysqli_query($koneksi, $query_edit);
    if (mysqli_num_rows($result_edit) > 0) {
        $edit_buku = mysqli_fetch_assoc($result_edit);
    }
}
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4"><?php echo $edit_buku ? 'Edit Data Buku' : 'Input Data Buku'; ?></h2>

    <?php if ($pesan_notifikasi): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $pesan_notifikasi; ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8 max-w-xl mx-auto">
        <form action="proses_admin.php" method="POST" enctype="multipart/form-data">
            <?php if ($edit_buku): ?>
                <input type="hidden" name="id_buku" value="<?php echo $edit_buku['id_buku']; ?>">
            <?php endif; ?>
            <div class="mb-4">
                <label for="judul" class="block text-gray-700 text-sm font-bold mb-2">Judul Buku:</label>
                <input type="text" id="judul" name="judul" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($edit_buku['judul'] ?? ''); ?>" required>
            </div>
            <div class="mb-4">
                <label for="penulis" class="block text-gray-700 text-sm font-bold mb-2">Penulis:</label>
                <input type="text" id="penulis" name="penulis" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($edit_buku['penulis'] ?? ''); ?>" required>
            </div>
            <div class="mb-4">
                <label for="penerbit" class="block text-gray-700 text-sm font-bold mb-2">Penerbit:</label>
                <input type="text" id="penerbit" name="penerbit" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($edit_buku['penerbit'] ?? ''); ?>" required>
            </div>
            <div class="mb-4">
                <label for="tahun_terbit" class="block text-gray-700 text-sm font-bold mb-2">Tahun Terbit:</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($edit_buku['tahun_terbit'] ?? ''); ?>" required>
            </div>
            <div class="mb-4">
                <label for="stok" class="block text-gray-700 text-sm font-bold mb-2">Stok:</label>
                <input type="number" id="stok" name="stok" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($edit_buku['stok'] ?? ''); ?>" required min="0">
            </div>
            <div class="mb-6">
                <label for="gambar_buku" class="block text-gray-700 text-sm font-bold mb-2">Gambar Buku:</label>
                <input type="file" id="gambar_buku" name="gambar_buku" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                <?php if ($edit_buku && $edit_buku['gambar_buku']): ?>
                    <p class="text-sm text-gray-600 mt-2">Gambar saat ini: <a href="../assets/images/<?php echo htmlspecialchars($edit_buku['gambar_buku']); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($edit_buku['gambar_buku']); ?></a></p>
                    <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($edit_buku['gambar_buku']); ?>">
                <?php endif; ?>
                <p class="text-xs text-gray-500 mt-1">Ukuran maksimal 2MB, format JPG, JPEG, PNG.</p>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="<?php echo $edit_buku ? 'update_buku' : 'tambah_buku'; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <?php echo $edit_buku ? 'Update Buku' : 'Tambah Buku'; ?>
                </button>
                <?php if ($edit_buku): ?>
                    <a href="buku.php" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Batal Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <h2 class="text-2xl font-bold mb-4">Daftar Buku Tersedia</h2>
    <?php if (mysqli_num_rows($result_buku) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">No.</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Gambar</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Judul</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Penulis</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Penerbit</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tahun</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Stok</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $no = 1; while ($buku = mysqli_fetch_assoc($result_buku)): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo $no++; ?></td>
                            <td class="py-3 px-4">
                                <img src="../assets/images/<?php echo htmlspecialchars($buku['gambar_buku'] ?: 'no-image.jpg'); ?>" alt="<?php echo htmlspecialchars($buku['judul']); ?>" class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($buku['judul']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($buku['penulis']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($buku['penerbit']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($buku['tahun_terbit']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($buku['stok']); ?></td>
                            <td class="py-3 px-4">
                                <a href="buku.php?edit=<?php echo $buku['id_buku']; ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm mr-2">Edit</a>
                                <a href="proses_admin.php?hapus_buku=<?php echo $buku['id_buku']; ?>"
                                   onclick="return confirm('Anda yakin ingin menghapus buku ini? Semua data peminjaman terkait akan terpengaruh.')"
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
        <p class="text-center text-gray-600">Belum ada buku terdaftar.</p>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>