<?php
// judul halaman
$page_title = "Dashboard Admin";

require_once "../koneksi/koneksi.php";

// Hitung total transaksi
$total_transaksi = $conn->query("SELECT COUNT(*) AS jml FROM transaksi")->fetch_assoc()['jml'] ?? 0;

// Hitung total menu kopi
$total_menu = $conn->query("SELECT COUNT(*) AS jml FROM menu")->fetch_assoc()['jml'] ?? 0;

// Hitung bahan hampir habis (misal stok < 50)
$bahan_habis = $conn->query("SELECT COUNT(*) AS jml FROM stok WHERE jumlah < 50")->fetch_assoc()['jml'] ?? 0;

// Hitung user terdaftar
$total_user = $conn->query("SELECT COUNT(*) AS jml FROM users")->fetch_assoc()['jml'] ?? 0;

// mulai tangkap konten
ob_start();
?>

<h3 class="mb-4">Dashboard Admin Kedai Kopi 69</h3>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Transaksi</h6>
                <h3 class="fw-bold"><?= $total_transaksi ?></h3>
                <small class="text-success">+0 hari ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-muted">Menu Kopi</h6>
                <h3 class="fw-bold"><?= $total_menu ?></h3>
                <small class="text-muted">Total menu terdaftar</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-muted">Bahan Hampir Habis</h6>
                <h3 class="fw-bold"><?= $bahan_habis ?></h3>
                <small class="text-warning">Perlu restock</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-muted">User Terdaftar</h6>
                <h3 class="fw-bold"><?= $total_user ?></h3>
                <small class="text-muted">Admin / Kasir / Owner</small>
            </div>
        </div>
    </div>
</div>

<?php
// ambil isi buffer ke variabel $content
$content = ob_get_clean();

// panggil layout
include "layout.php";