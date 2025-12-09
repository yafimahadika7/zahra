<?php
$page_title = "Dashboard Kasir";
require_once "../koneksi/koneksi.php";

// =======================
// HITUNG TOTAL TRANSAKSI HARI INI
// =======================
$today = date("Y-m-d");

$q_transaksi = $conn->query("
    SELECT COUNT(*) AS jml 
    FROM transaksi 
    WHERE DATE(tanggal) = '$today'
");
$total_transaksi_hari_ini = $q_transaksi->fetch_assoc()['jml'] ?? 0;

// =======================
// HITUNG TOTAL OMZET HARI INI
// =======================
$q_omzet = $conn->query("
    SELECT SUM(total_harga) AS omzet 
    FROM transaksi 
    WHERE DATE(tanggal) = '$today'
");
$total_omzet_hari_ini = $q_omzet->fetch_assoc()['omzet'] ?? 0;

// =======================
// MENU TERLARIS HARI INI
// =======================
$q_laris = $conn->query("
    SELECT m.nama_menu, SUM(d.qty) AS total
    FROM detail_transaksi d
    JOIN menu m ON d.id_menu = m.id_menu
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    WHERE DATE(t.tanggal) = '$today'
    GROUP BY d.id_menu
    ORDER BY total DESC
    LIMIT 1
");

$menu_terlaris = "-";
$jumlah_terjual = 0;

if ($q_laris && $q_laris->num_rows > 0) {
    $row = $q_laris->fetch_assoc();
    $menu_terlaris = $row['nama_menu'];
    $jumlah_terjual = $row['total'];
}

// =======================
// BAHAN HAMPIR HABIS
// =======================
$q_stok_habis = $conn->query("SELECT COUNT(*) AS jml FROM stok WHERE jumlah < 50");
$bahan_habis = $q_stok_habis->fetch_assoc()['jml'] ?? 0;

// Mulai buffer
ob_start();
?>

<h3 class="mb-4">Dashboard Kasir Kedai Kopi 69</h3>

<style>
    .dashboard-card {
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>

<div class="row g-4">

    <div class="col-md-3">
        <div class="card shadow-sm border-0 dashboard-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Transaksi Hari Ini</h6>
                <h3 class="fw-bold mb-1"><?= $total_transaksi_hari_ini ?></h3>
                <small class="text-success">Total transaksi masuk</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 dashboard-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Omzet Hari Ini</h6>
                <h3 class="fw-bold mb-1">Rp <?= number_format($total_omzet_hari_ini, 0, ',', '.') ?></h3>
                <small class="text-success">Pendapatan hari ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 dashboard-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Menu Terlaris Hari Ini</h6>
                <h5 class="fw-bold mb-1"><?= $menu_terlaris ?></h5>
                <small class="text-muted"><?= $jumlah_terjual ?> terjual</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 dashboard-card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Bahan Hampir Habis</h6>
                <h3 class="fw-bold mb-1"><?= $bahan_habis ?></h3>
                <small class="text-warning">Perlu restock</small>
            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
include "layout.php";
