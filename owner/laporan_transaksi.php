<?php
$page_title = "Laporan Transaksi";
require "../koneksi/koneksi.php";

session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

// ==========================
// FILTER TANGGAL
// ==========================

$tgl_mulai = $_GET['tgl_mulai'] ?? date("Y-m-01");
$tgl_selesai = $_GET['tgl_selesai'] ?? date("Y-m-d");

// QUERY LAPORAN
$q = $conn->query("
    SELECT t.*, u.nama AS kasir
    FROM transaksi t
    JOIN users u ON t.id_user = u.id_user
    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ORDER BY t.id_transaksi DESC
");

// HITUNG OMZET
$q_omzet = $conn->query("
    SELECT SUM(total_harga) AS omzet
    FROM transaksi
    WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");

$total_omzet = $q_omzet->fetch_assoc()['omzet'] ?? 0;

ob_start();
?>

<style>
    .filter-box {
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .table thead {
        background: #3b3024;
        color: white;
    }

    .detail-box {
        background: #f7f3ee;
        padding: 12px;
        border-radius: 6px;
        margin-top: 10px;
    }

    .total-footer {
        font-size: 18px;
        text-align: right;
        margin-top: 15px;
        font-weight: bold;
        color: #3b3024;
    }
</style>

<h3 class="mb-4">Laporan Transaksi</h3>

<!-- FILTER -->
<div class="filter-box">
    <form method="GET" class="row g-3 align-items-end">

        <div class="col-md-4">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" value="<?= $tgl_mulai ?>" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" value="<?= $tgl_selesai ?>" class="form-control" required>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Tampilkan
            </button>
        </div>

    </form>
</div>

<!-- HASIL LAPORAN -->
<div class="card shadow-sm">
    <div class="card-body">

        <h5 class="mb-3">
            Periode:
            <b><?= date("d M Y", strtotime($tgl_mulai)) ?></b>
            –
            <b><?= date("d M Y", strtotime($tgl_selesai)) ?></b>
        </h5>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Total Harga</th>
                    <th>Pembayaran</th>
                    <th>Kembalian</th>
                    <th width="10%">Detail</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $no = 1;
                if ($q->num_rows > 0):
                    while ($row = $q->fetch_assoc()):
                        ?>

                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date("d M Y H:i", strtotime($row['tanggal'])) ?></td>
                            <td><?= $row['kasir'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['pembayaran'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['kembali'], 0, ',', '.') ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                    data-bs-target="#detail<?= $row['id_transaksi'] ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- DETAIL TRANSAKSI -->
                        <tr class="collapse" id="detail<?= $row['id_transaksi'] ?>">
                            <td colspan="7">
                                <div class="detail-box">

                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Menu</th>
                                                <th>Qty</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $id_t = $row['id_transaksi'];
                                            $detail = $conn->query("
                                        SELECT dt.*, m.nama_menu 
                                        FROM detail_transaksi dt
                                        JOIN menu m ON dt.id_menu = m.id_menu
                                        WHERE dt.id_transaksi = '$id_t'
                                    ");

                                            while ($d = $detail->fetch_assoc()):
                                                ?>
                                                <tr>
                                                    <td><?= $d['nama_menu'] ?></td>
                                                    <td><?= $d['qty'] ?></td>
                                                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>

                                    <div class="text-end mt-2 fw-bold">
                                        Total: Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                    </div>

                                </div>
                            </td>
                        </tr>

                        <?php
                    endwhile;
                else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada transaksi untuk periode ini.</td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-footer">
            Total Omzet: Rp <?= number_format($total_omzet, 0, ',', '.') ?>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
include "layout.php";
?>