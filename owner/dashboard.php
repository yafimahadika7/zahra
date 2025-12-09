<?php
$page_title = "Dashboard Owner";
require "../koneksi/koneksi.php";

session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

$today = date("Y-m-d");
$month = date("Y-m");

// ===== Transaksi Hari Ini =====
$q1 = $conn->query("SELECT COUNT(*) AS jml FROM transaksi WHERE DATE(tanggal) = '$today'");
$total_transaksi_hari_ini = $q1->fetch_assoc()['jml'] ?? 0;

// ===== Omzet Hari Ini =====
$q2 = $conn->query("SELECT SUM(total_harga) AS omzet FROM transaksi WHERE DATE(tanggal) = '$today'");
$total_omzet_hari_ini = $q2->fetch_assoc()['omzet'] ?? 0;

// ===== Menu Terlaris Hari Ini =====
$q3 = $conn->query("
    SELECT m.nama_menu, SUM(d.qty) AS total
    FROM detail_transaksi d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    JOIN menu m ON d.id_menu = m.id_menu
    WHERE DATE(t.tanggal) = '$today'
    GROUP BY m.id_menu
    ORDER BY total DESC
    LIMIT 1
");

$menu_terlaris = "-";
$jumlah_terjual = 0;

if ($q3 && $q3->num_rows > 0) {
    $r = $q3->fetch_assoc();
    $menu_terlaris = $r['nama_menu'];
    $jumlah_terjual = $r['total'];
}

// ===== Bahan Hampir Habis =====
$q4 = $conn->query("SELECT COUNT(*) AS jml FROM stok WHERE jumlah < 50");
$bahan_habis = $q4->fetch_assoc()['jml'] ?? 0;

// ===== Transaksi Bulan Ini =====
$q5 = $conn->query("SELECT COUNT(*) AS jml FROM transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$month'");
$total_transaksi_bulan = $q5->fetch_assoc()['jml'] ?? 0;

// ===== Omzet Bulan Ini =====
$q6 = $conn->query("SELECT SUM(total_harga) AS omzet FROM transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$month'");
$total_omzet_bulan = $q6->fetch_assoc()['omzet'] ?? 0;

ob_start();
?>

<!-- STYLE CARD RAPIH -->
<style>
    .stat-card {
        border-radius: 12px;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 20px !important;
        transition: .2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-title {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #3b3024;
    }

    .stat-desc {
        font-size: 13px;
    }
</style>

<h3 class="mb-4">Dashboard Owner Kedai Kopi 69</h3>

<!-- BARIS 1 -->
<div class="row g-4 mb-2">

    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 stat-card">
            <div>
                <div class="stat-title">Transaksi Hari Ini</div>
                <div class="stat-value"><?= $total_transaksi_hari_ini ?></div>
                <div class="stat-desc text-success">Jumlah transaksi masuk</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 stat-card">
            <div>
                <div class="stat-title">Omzet Hari Ini</div>
                <div class="stat-value">Rp <?= number_format($total_omzet_hari_ini, 0, ',', '.') ?></div>
                <div class="stat-desc text-success">Pendapatan hari ini</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 stat-card">
            <div>
                <div class="stat-title">Menu Terlaris Hari Ini</div>
                <div class="stat-value" style="font-size:22px"><?= $menu_terlaris ?></div>
                <div class="stat-desc text-muted"><?= $jumlah_terjual ?> terjual</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 stat-card">
            <div>
                <div class="stat-title">Bahan Hampir Habis</div>
                <div class="stat-value"><?= $bahan_habis ?></div>
                <div class="stat-desc text-warning">Harus segera restock</div>
            </div>
        </div>
    </div>

</div>

<!-- BARIS 2 -->
<div class="row g-4">

    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 stat-card">
            <div>
                <div class="stat-title">Total Transaksi Bulan Ini</div>
                <div class="stat-value"><?= $total_transaksi_bulan ?></div>
                <div class="stat-desc text-primary">Akumulasi transaksi</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 stat-card">
            <div>
                <div class="stat-title">Omzet Bulan Ini</div>
                <div class="stat-value">Rp <?= number_format($total_omzet_bulan, 0, ',', '.') ?></div>
                <div class="stat-desc text-primary">Pendapatan bulan berjalan</div>
            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
include "layout.php";
?>