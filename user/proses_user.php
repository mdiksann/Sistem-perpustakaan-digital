<?php
// user/proses_user.php
session_start();
include_once '../config/koneksi.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['level'] != 'user') {
    header('Location: ../auth/login.php');
    exit();
}

// Proses Peminjaman Buku
if (isset($_POST['pinjam_buku'])) {
    $id_buku = bersihkan_input($_POST['id_buku']);
    $id_pengguna = bersihkan_input($_POST['id_pengguna']);
    $tanggal_pinjam = bersihkan_input($_POST['tanggal_pinjam']);
    $tanggal_kembali_estimasi = bersihkan_input($_POST['tanggal_kembali_estimasi']);

    // Validasi stok buku
    $query_cek_stok = "SELECT stok FROM buku WHERE id_buku = '$id_buku'";
    $result_cek_stok = mysqli_query($koneksi, $query_cek_stok);
    $data_buku = mysqli_fetch_assoc($result_cek_stok);

    if ($data_buku['stok'] > 0) {
        // Cek apakah pengguna sudah meminjam buku ini dan belum dikembalikan
        $query_cek_pinjaman = "SELECT * FROM peminjaman WHERE id_pengguna = '$id_pengguna' AND id_buku = '$id_buku' AND status_pinjam = 'dipinjam'";
        $result_cek_pinjaman = mysqli_query($koneksi, $query_cek_pinjaman);

        if (mysqli_num_rows($result_cek_pinjaman) > 0) {
            $_SESSION['pesan_error_pinjam'] = "Anda sudah meminjam buku ini dan belum mengembalikannya.";
            header("Location: pinjam_buku.php?id_buku=$id_buku");
            exit();
        }

        // Mulai transaksi
        mysqli_begin_transaction($koneksi);

        try {
            // Insert data peminjaman
            $query_pinjam = "INSERT INTO peminjaman (id_pengguna, id_buku, tanggal_pinjam, tanggal_kembali_estimasi, status_pinjam) VALUES ('$id_pengguna', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali_estimasi', 'dipinjam')";
            $result_pinjam = mysqli_query($koneksi, $query_pinjam);

            if (!$result_pinjam) {
                throw new Exception("Gagal menyimpan data peminjaman: " . mysqli_error($koneksi));
            }

            // Kurangi stok buku
            $query_update_stok = "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'";
            $result_update_stok = mysqli_query($koneksi, $query_update_stok);

            if (!$result_update_stok) {
                throw new Exception("Gagal mengurangi stok buku: " . mysqli_error($koneksi));
            }

            mysqli_commit($koneksi);
            $_SESSION['pesan_notifikasi_user'] = "Buku berhasil dipinjam!";
            header('Location: riwayat_pinjam.php');
            exit();

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['pesan_error_pinjam'] = "Terjadi kesalahan: " . $e->getMessage();
            header("Location: pinjam_buku.php?id_buku=$id_buku");
            exit();
        }
    } else {
        $_SESSION['pesan_error_pinjam'] = "Stok buku tidak mencukupi.";
        header("Location: pinjam_buku.php?id_buku=$id_buku");
        exit();
    }
}

// Proses Pengembalian Buku
if (isset($_GET['kembalikan'])) {
    $id_peminjaman = bersihkan_input($_GET['kembalikan']);
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
            throw new Exception("Gagal mengembalikan buku: " . mysqli_error($koneksi));
        }

        // Tambah stok buku
        $query_tambah_stok = "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'";
        $result_tambah_stok = mysqli_query($koneksi, $query_tambah_stok);

        if (!$result_tambah_stok) {
            throw new Exception("Gagal menambah stok buku: " . mysqli_error($koneksi));
        }

        mysqli_commit($koneksi);
        $_SESSION['pesan_notifikasi_user'] = "Buku berhasil dikembalikan!";
        header('Location: riwayat_pinjam.php');
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan_notifikasi_user'] = "Terjadi kesalahan saat mengembalikan buku: " . $e->getMessage();
        header('Location: riwayat_pinjam.php');
        exit();
    }
}
?>