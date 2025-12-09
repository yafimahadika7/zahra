<?php
$page_title = "Riwayat Transaksi";
require "../koneksi/koneksi.php";

session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

// FILTER TANGGAL
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date("Y-m-d");

$q = $conn->query("
    SELECT t.*, u.nama AS kasir
    FROM transaksi t
    JOIN users u ON t.id_user = u.id_user
    WHERE DATE(t.tanggal) = '$tanggal'
    ORDER BY t.id_transaksi DESC
");

ob_start();
?>

<h3 class="mb-4">Riwayat Transaksi</h3>

<!-- FILTER TANGGAL -->
<form method="GET" class="row mb-3">
    <div class="col-md-3">
        <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary">
            <i class="bi bi-search"></i> Tampilkan
        </button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
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
                $modal_data = ""; // tempat menyimpan modal
                if ($q->num_rows > 0):
                    while ($row = $q->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date("d M Y H:i", strtotime($row['tanggal'])) ?></td>
                            <td><?= $row['kasir'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['pembayaran'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['kembali'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalDetail<?= $row['id_transaksi'] ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <?php
                        // SIMPAN MODAL DETAIL (dikeluarkan dari tabel)
                        ob_start();
                        ?>

                        <div class="modal fade" id="modalDetail<?= $row['id_transaksi'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header bg-dark text-white">
                                        <h5 class="modal-title">
                                            Detail Transaksi #<?= $row['id_transaksi'] ?>
                                        </h5>
                                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p><b>Tanggal:</b> <?= $row['tanggal'] ?></p>
                                        <p><b>Kasir:</b> <?= $row['kasir'] ?></p>

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

                                        <h5 class="text-end mt-3">
                                            Total: <b>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></b>
                                        </h5>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <?php
                        $modal_data .= ob_get_clean();
                    endwhile;
                else:
                    ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada transaksi hari ini.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>
</div>

<!-- CETAK SEMUA MODAL SEKALIGUS DI LUAR TABEL -->
<?= $modal_data ?>

<?php
$content = ob_get_clean();
include "layout.php";
?>