<?php
// admin/proses_admin.php
session_start();
include_once '../config/koneksi.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['level'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fungsi Tambah Buku (Create)
if (isset($_POST['tambah_buku'])) {
    $judul = bersihkan_input($_POST['judul']);
    $penulis = bersihkan_input($_POST['penulis']);
    $penerbit = bersihkan_input($_POST['penerbit']);
    $tahun_terbit = bersihkan_input($_POST['tahun_terbit']);
    $stok = bersihkan_input($_POST['stok']);

    $nama_gambar = '';
    if (isset($_FILES['gambar_buku']) && $_FILES['gambar_buku']['error'] == 0) {
        $target_dir = "../assets/images/";
        $nama_gambar = uniqid() . '_' . basename($_FILES["gambar_buku"]["name"]);
        $target_file = $target_dir . $nama_gambar;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Validasi file
        $uploadOk = 1;
        $check = getimagesize($_FILES["gambar_buku"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $_SESSION['pesan_notifikasi_admin'] = "File bukan gambar.";
            $uploadOk = 0;
        }

        if ($_FILES["gambar_buku"]["size"] > 2000000) { // 2MB
            $_SESSION['pesan_notifikasi_admin'] = "Ukuran gambar terlalu besar (maks 2MB).";
            $uploadOk = 0;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $_SESSION['pesan_notifikasi_admin'] = "Hanya format JPG, JPEG, PNG yang diizinkan.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            header('Location: buku.php');
            exit();
        } else {
            if (move_uploaded_file($_FILES["gambar_buku"]["tmp_name"], $target_file)) {
                // Gambar berhasil diupload
            } else {
                $_SESSION['pesan_notifikasi_admin'] = "Gagal mengupload gambar.";
                header('Location: buku.php');
                exit();
            }
        }
    }

    $query = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, gambar_buku, stok) VALUES ('$judul', '$penulis', '$penerbit', '$tahun_terbit', '$nama_gambar', '$stok')";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['pesan_notifikasi_admin'] = "Buku berhasil ditambahkan!";
    } else {
        $_SESSION['pesan_notifikasi_admin'] = "Gagal menambahkan buku: " . mysqli_error($koneksi);
    }
    header('Location: buku.php');
    exit();
}

// Fungsi Update Buku (Update)
if (isset($_POST['update_buku'])) {
    $id_buku = bersihkan_input($_POST['id_buku']);
    $judul = bersihkan_input($_POST['judul']);
    $penulis = bersihkan_input($_POST['penulis']);
    $penerbit = bersihkan_input($_POST['penerbit']);
    $tahun_terbit = bersihkan_input($_POST['tahun_terbit']);
    $stok = bersihkan_input($_POST['stok']);
    $gambar_lama = bersihkan_input($_POST['gambar_lama'] ?? '');

    $nama_gambar = $gambar_lama; // Default menggunakan gambar lama

    if (isset($_FILES['gambar_buku']) && $_FILES['gambar_buku']['error'] == 0) {
        $target_dir = "../assets/images/";
        $nama_gambar_baru = uniqid() . '_' . basename($_FILES["gambar_buku"]["name"]);
        $target_file = $target_dir . $nama_gambar_baru;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $uploadOk = 1;
        $check = getimagesize($_FILES["gambar_buku"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $_SESSION['pesan_notifikasi_admin'] = "File bukan gambar.";
            $uploadOk = 0;
        }

        if ($_FILES["gambar_buku"]["size"] > 2000000) { // 2MB
            $_SESSION['pesan_notifikasi_admin'] = "Ukuran gambar terlalu besar (maks 2MB).";
            $uploadOk = 0;
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $_SESSION['pesan_notifikasi_admin'] = "Hanya format JPG, JPEG, PNG yang diizinkan.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            header('Location: buku.php?edit=' . $id_buku);
            exit();
        } else {
            if (move_uploaded_file($_FILES["gambar_buku"]["tmp_name"], $target_file)) {
                // Hapus gambar lama jika ada dan bukan gambar default
                if ($gambar_lama && file_exists($target_dir . $gambar_lama) && $gambar_lama != 'no-image.jpg') {
                    unlink($target_dir . $gambar_lama);
                }
                $nama_gambar = $nama_gambar_baru;
            } else {
                $_SESSION['pesan_notifikasi_admin'] = "Gagal mengupload gambar baru.";
                header('Location: buku.php?edit=' . $id_buku);
                exit();
            }
        }
    }

    $query = "UPDATE buku SET judul = '$judul', penulis = '$penulis', penerbit = '$penerbit', tahun_terbit = '$tahun_terbit', gambar_buku = '$nama_gambar', stok = '$stok' WHERE id_buku = '$id_buku'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['pesan_notifikasi_admin'] = "Buku berhasil diperbarui!";
    } else {
        $_SESSION['pesan_notifikasi_admin'] = "Gagal memperbarui buku: " . mysqli_error($koneksi);
    }
    header('Location: buku.php');
    exit();
}

// Fungsi Hapus Buku (Delete)
if (isset($_GET['hapus_buku'])) {
    $id_buku = bersihkan_input($_GET['hapus_buku']);

    // Ambil nama gambar sebelum menghapus data buku
    $query_get_gambar = "SELECT gambar_buku FROM buku WHERE id_buku = '$id_buku'";
    $result_get_gambar = mysqli_query($koneksi, $query_get_gambar);
    $data_buku = mysqli_fetch_assoc($result_get_gambar);
    $nama_gambar = $data_buku['gambar_buku'] ?? '';

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Hapus entri peminjaman terkait buku ini terlebih dahulu
        $query_hapus_peminjaman = "DELETE FROM peminjaman WHERE id_buku = '$id_buku'";
        if (!mysqli_query($koneksi, $query_hapus_peminjaman)) {
            throw new Exception("Gagal menghapus riwayat peminjaman terkait buku: " . mysqli_error($koneksi));
        }

        // Hapus data buku
        $query_hapus_buku = "DELETE FROM buku WHERE id_buku = '$id_buku'";
        if (mysqli_query($koneksi, $query_hapus_buku)) {
            // Hapus file gambar jika ada dan bukan gambar default
            if ($nama_gambar && file_exists("../assets/images/" . $nama_gambar) && $nama_gambar != 'no-image.jpg') {
                unlink("../assets/images/" . $nama_gambar);
            }
            mysqli_commit($koneksi);
            $_SESSION['pesan_notifikasi_admin'] = "Buku berhasil dihapus!";
        } else {
            throw new Exception("Gagal menghapus buku: " . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan_notifikasi_admin'] = "Terjadi kesalahan saat menghapus buku: " . $e->getMessage();
    }
    header('Location: buku.php');
    exit();
}

// Fungsi Hapus Anggota
if (isset($_GET['hapus_anggota'])) {
    $id_pengguna = bersihkan_input($_GET['hapus_anggota']);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Cek apakah anggota ini memiliki peminjaman yang belum dikembalikan
        $query_cek_pinjam_aktif = "SELECT COUNT(*) AS total_pinjam FROM peminjaman WHERE id_pengguna = '$id_pengguna' AND status_pinjam = 'dipinjam'";
        $result_cek = mysqli_query($koneksi, $query_cek_pinjam_aktif);
        $data_pinjam_aktif = mysqli_fetch_assoc($result_cek);

        if ($data_pinjam_aktif['total_pinjam'] > 0) {
            throw new Exception("Tidak dapat menghapus anggota karena masih memiliki buku yang belum dikembalikan.");
        }

        // Hapus semua riwayat peminjaman anggota ini
        $query_hapus_riwayat_pinjam = "DELETE FROM peminjaman WHERE id_pengguna = '$id_pengguna'";
        if (!mysqli_query($koneksi, $query_hapus_riwayat_pinjam)) {
            throw new Exception("Gagal menghapus riwayat peminjaman anggota: " . mysqli_error($koneksi));
        }

        // Hapus data anggota
        $query_hapus_anggota = "DELETE FROM pengguna WHERE id_pengguna = '$id_pengguna' AND level = 'user'";
        if (mysqli_query($koneksi, $query_hapus_anggota)) {
            mysqli_commit($koneksi);
            $_SESSION['pesan_notifikasi_admin'] = "Anggota berhasil dihapus!";
        } else {
            throw new Exception("Gagal menghapus anggota: " . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan_notifikasi_admin'] = "Terjadi kesalahan saat menghapus anggota: " . $e->getMessage();
    }
    header('Location: anggota.php');
    exit();
}


// Proses Pengembalian Buku dari Admin (Mirip dengan user, tapi di admin)
if (isset($_GET['kembalikan_admin'])) {
    $id_peminjaman = bersihkan_input($_GET['kembalikan_admin']);
    $tanggal_kembali_aktual = date('Y-m-d');

    // Dapatkan id_buku dari peminjaman
    $query_get_buku_id = "SELECT id_buku, tanggal_kembali_estimasi FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'";
    $result_get_buku_id = mysqli_query($koneksi, $query_get_buku_id);
    $data_pinjam = mysqli_fetch_assoc($result_get_buku_id);
    $id_buku = $data_pinjam['id_buku'];
    $tanggal_kembali_estimasi = $data_pinjam['tanggal_kembali_estimasi'];

    $status_pinjam = 'dikembalikan';
    if ($tanggal_kembali_aktual > $tanggal_kembali_estimasi) {
        $status_pinjam = 'terlambat';
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Update status peminjaman dan tanggal kembali aktual
        $query_kembalikan = "UPDATE peminjaman SET tanggal_kembali_aktual = '$tanggal_kembali_aktual', status_pinjam = '$status_pinjam' WHERE id_peminjaman = '$id_peminjaman' AND status_pinjam = 'dipinjam'";
        $result_kembalikan = mysqli_query($koneksi, $query_kembalikan);

        if (!$result_kembalikan) {
            throw new Exception("Gagal mengembalikan buku (admin): " . mysqli_error($koneksi));
        }

        // Tambah stok buku
        $query_tambah_stok = "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'";
        $result_tambah_stok = mysqli_query($koneksi, $query_tambah_stok);

        if (!$result_tambah_stok) {
            throw new Exception("Gagal menambah stok buku (admin): " . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        $_SESSION['pesan_notifikasi_admin'] = "Buku berhasil ditandai dikembalikan oleh admin!";
        header('Location: laporan_pinjam.php');
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan_notifikasi_admin'] = "Terjadi kesalahan saat menandai pengembalian buku: " . $e->getMessage();
        header('Location: laporan_pinjam.php');
        exit();
    }
}
?>