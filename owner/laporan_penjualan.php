<?php
$page_title = "Laporan Penjualan";
require "../koneksi/koneksi.php";

session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

// =============================
// FILTER TANGGAL
// =============================
$tgl_mulai = $_GET['tgl_mulai'] ?? date("Y-m-01");
$tgl_selesai = $_GET['tgl_selesai'] ?? date("Y-m-d");

// =============================
// QUERY PENJUALAN PER MENU
// =============================
$q = $conn->query("
    SELECT 
        m.id_menu,
        m.nama_menu,
        SUM(d.qty) AS total_qty,
        SUM(d.subtotal) AS total_subtotal
    FROM detail_transaksi d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    JOIN menu m ON d.id_menu = m.id_menu
    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    GROUP BY m.id_menu
    ORDER BY total_qty DESC
");

// HITUNG TOTAL OMZET
$q_omzet = $conn->query("
    SELECT SUM(total_harga) AS omzet
    FROM transaksi
    WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");
$total_omzet = $q_omzet->fetch_assoc()['omzet'] ?? 0;

// HITUNG TOTAL QTY
$q_qty = $conn->query("
    SELECT SUM(qty) AS qty
    FROM detail_transaksi d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");
$total_qty = $q_qty->fetch_assoc()['qty'] ?? 0;

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

    .summary-card {
        background: #fff;
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
        border-left: 5px solid #8b6f47;
    }

    .table thead {
        background: #3b3024;
        color: #fff;
    }

    .detail-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
</style>

<h3 class="mb-4">📊 Laporan Penjualan</h3>

<!-- FILTER -->
<div class="filter-box">
    <form method="GET" class="row g-3 align-items-end">

        <div class="col-md-4">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>" required>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Tampilkan
            </button>
        </div>

    </form>
</div>

<!-- SUMMARY -->
<div class="row mb-4">

    <div class="col-md-4">
        <div class="summary-card">
            <h6 class="text-muted">Total Penjualan (Qty)</h6>
            <h3 class="fw-bold"><?= $total_qty ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="summary-card">
            <h6 class="text-muted">Total Omzet</h6>
            <h3 class="fw-bold">Rp <?= number_format($total_omzet, 0, ',', '.') ?></h3>
        </div>
    </div>

</div>

<!-- TABEL PENJUALAN -->
<div class="card shadow-sm">
    <div class="card-body">

        <h5 class="mb-3">
            Periode:
            <b><?= date("d M Y", strtotime($tgl_mulai)) ?></b>
            –
            <b><?= date("d M Y", strtotime($tgl_selesai)) ?></b>
        </h5>

        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Menu</th>
                    <th>Total Qty</th>
                    <th>Total Pendapatan</th>
                    <th class="text-center" width="10%">Detail</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $no = 1;
                $modals = ""; // Tempat menyimpan modal
                
                if ($q->num_rows > 0):
                    while ($row = $q->fetch_assoc()):
                        ?>

                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_menu'] ?></td>
                            <td><?= $row['total_qty'] ?></td>
                            <td>Rp <?= number_format($row['total_subtotal'], 0, ',', '.') ?></td>

                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-primary btn-sm detail-btn" data-bs-toggle="modal"
                                        data-bs-target="#modalDetail<?= $row['id_menu'] ?>">
                                        <i class="bi bi-eye text-white"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>

                        <?php
                        // SIMPAN MODAL DI VARIABLE
                        $id_menu = $row['id_menu'];
                        $nama_menu = $row['nama_menu'];

                        $detail = $conn->query("
                    SELECT dt.qty, dt.subtotal, t.tanggal
                    FROM detail_transaksi dt
                    JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                    WHERE dt.id_menu = '$id_menu'
                    AND DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                    ORDER BY t.tanggal DESC
                ");

                        $modal_html = '
<div class="modal fade" id="modalDetail' . $id_menu . '" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Detail Penjualan – ' . $nama_menu . '</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                ';

                        while ($d = $detail->fetch_assoc()) {
                            $modal_html .= '
                        <tr>
                            <td>' . date("d M Y H:i", strtotime($d['tanggal'])) . '</td>
                            <td>' . $d['qty'] . '</td>
                            <td>Rp ' . number_format($d['subtotal'], 0, ',', '.') . '</td>
                        </tr>';
                        }

                        $modal_html .= '
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>';

                        $modals .= $modal_html;

                    endwhile;
                else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada penjualan pada periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<!-- TEMPAT OUTPUT SEMUA MODAL -->
<?= $modals ?>

<?php
$content = ob_get_clean();
include "layout.php";
?>