<?php
// user/index.php
include_once '../includes/header.php';
include_once '../includes/sidebar_user.php';
include_once '../config/koneksi.php';

// Fungsi Cari Buku
$cari = '';
if (isset($_GET['cari'])) {
    $cari = bersihkan_input($_GET['cari']);
    $query_buku = "SELECT * FROM buku WHERE judul LIKE '%$cari%' OR penulis LIKE '%$cari%' OR penerbit LIKE '%$cari%' ORDER BY judul ASC";
} else {
    $query_buku = "SELECT * FROM buku ORDER BY judul ASC";
}
$result_buku = mysqli_query($koneksi, $query_buku);

$pesan_notifikasi = '';
if (isset($_SESSION['pesan_notifikasi_user'])) {
    $pesan_notifikasi = $_SESSION['pesan_notifikasi_user'];
    unset($_SESSION['pesan_notifikasi_user']);
}
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Daftar Buku</h2>

    <?php if ($pesan_notifikasi): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $pesan_notifikasi; ?></span>
        </div>
    <?php endif; ?>

    <form action="" method="GET" class="mb-6 flex items-center">
        <input type="text" name="cari" placeholder="Cari judul, penulis, atau penerbit..." class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($cari); ?>">
        <button type="submit" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cari</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (mysqli_num_rows($result_buku) > 0): ?>
            <?php while ($buku = mysqli_fetch_assoc($result_buku)): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="../assets/images/<?php echo htmlspecialchars($buku['gambar_buku'] ?: 'no-image.jpg'); ?>" alt="<?php echo htmlspecialchars($buku['judul']); ?>" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($buku['judul']); ?></h3>
                        <p class="text-gray-700 text-sm mb-1">Penulis: <?php echo htmlspecialchars($buku['penulis']); ?></p>
                        <p class="text-gray-700 text-sm mb-1">Penerbit: <?php echo htmlspecialchars($buku['penerbit']); ?></p>
                        <p class="text-gray-700 text-sm mb-4">Tahun Terbit: <?php echo htmlspecialchars($buku['tahun_terbit']); ?></p>
                        <p class="text-gray-900 font-bold mb-4">Stok: <?php echo htmlspecialchars($buku['stok']); ?></p>
                        <?php if ($buku['stok'] > 0): ?>
                            <a href="pinjam_buku.php?id_buku=<?php echo $buku['id_buku']; ?>" class="block w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Pinjam Buku</a>
                        <?php else: ?>
                            <button class="block w-full text-center bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed" disabled>Stok Habis</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="col-span-full text-center text-gray-600">Tidak ada buku ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>