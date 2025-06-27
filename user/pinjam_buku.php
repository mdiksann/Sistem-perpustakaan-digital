<?php
// user/pinjam_buku.php
include_once '../includes/header.php';
include_once '../includes/sidebar_user.php';
include_once '../config/koneksi.php';

$id_buku = isset($_GET['id_buku']) ? (int)$_GET['id_buku'] : 0;
$buku = null;

if ($id_buku > 0) {
    $query_buku = "SELECT * FROM buku WHERE id_buku = $id_buku";
    $result_buku = mysqli_query($koneksi, $query_buku);
    if (mysqli_num_rows($result_buku) > 0) {
        $buku = mysqli_fetch_assoc($result_buku);
    }
}

if (!$buku) {
    $_SESSION['pesan_notifikasi_user'] = "Buku tidak ditemukan.";
    header('Location: index.php');
    exit();
}

$pesan_error = '';
if (isset($_SESSION['pesan_error_pinjam'])) {
    $pesan_error = $_SESSION['pesan_error_pinjam'];
    unset($_SESSION['pesan_error_pinjam']);
}
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Form Peminjaman Buku</h2>

    <?php if ($pesan_error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $pesan_error; ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <div class="flex items-center mb-6">
            <img src="../assets/images/<?php echo htmlspecialchars($buku['gambar_buku'] ?: 'no-image.jpg'); ?>" alt="<?php echo htmlspecialchars($buku['judul']); ?>" class="w-32 h-32 object-cover rounded-lg mr-6">
            <div>
                <h3 class="text-xl font-semibold mb-1"><?php echo htmlspecialchars($buku['judul']); ?></h3>
                <p class="text-gray-700 text-sm">Penulis: <?php echo htmlspecialchars($buku['penulis']); ?></p>
                <p class="text-gray-700 text-sm">Stok Tersedia: <?php echo htmlspecialchars($buku['stok']); ?></p>
            </div>
        </div>

        <form action="proses_user.php" method="POST">
            <input type="hidden" name="id_buku" value="<?php echo $buku['id_buku']; ?>">
            <input type="hidden" name="id_pengguna" value="<?php echo $_SESSION['id_pengguna']; ?>">

            <div class="mb-4">
                <label for="tanggal_pinjam" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Pinjam:</label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo date('Y-m-d'); ?>" readonly>
            </div>
            <div class="mb-6">
                <label for="tanggal_kembali_estimasi" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Kembali Estimasi (Contoh: 7 hari dari sekarang):</label>
                <input type="date" id="tanggal_kembali_estimasi" name="tanggal_kembali_estimasi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="pinjam_buku" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Konfirmasi Peminjaman
                </button>
                <a href="index.php" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>