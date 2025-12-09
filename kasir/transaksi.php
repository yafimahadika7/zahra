<?php
session_start();
require "../koneksi/koneksi.php";

$page_title = "Transaksi Penjualan";

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah item ke keranjang
if (isset($_POST['add_item'])) {
    $id_menu = $_POST['id_menu'];
    $qty = $_POST['qty'];

    // Ambil data menu
    $q = $conn->query("SELECT * FROM menu WHERE id_menu = '$id_menu'");
    $m = $q->fetch_assoc();

    $subtotal = $m['harga'] * $qty;

    $_SESSION['keranjang'][] = [
        'id_menu' => $id_menu,
        'nama_menu' => $m['nama_menu'],
        'harga' => $m['harga'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

// Hapus item
if (isset($_GET['hapus'])) {
    $index = $_GET['hapus'];
    unset($_SESSION['keranjang'][$index]);
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
}

// Hitung total
$total = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['subtotal'];
}

// MULAI BUFFER LAYOUT
ob_start();
?>

<h3 class="mb-4">Transaksi Penjualan</h3>

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <form method="POST">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pilih Menu</label>
                    <select class="form-select" name="id_menu" required>
                        <option value="">-- Pilih Menu --</option>

                        <?php
                        $menu = $conn->query("SELECT * FROM menu WHERE status = 'aktif'");
                        while ($m = $menu->fetch_assoc()):
                            ?>
                            <option value="<?= $m['id_menu'] ?>">
                                <?= $m['nama_menu'] ?> - Rp <?= number_format($m['harga'], 0, ',', '.') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Qty</label>
                    <input type="number" name="qty" class="form-control" min="1" required>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button name="add_item" class="btn btn-primary w-100">
                        <i class="bi bi-cart-plus"></i> Tambah
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>



<!-- TABLE KERANJANG -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5>Keranjang Belanja</h5>

        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($_SESSION['keranjang'])): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada item</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($_SESSION['keranjang'] as $i => $item): ?>
                        <tr>
                            <td><?= $item['nama_menu'] ?></td>
                            <td><?= $item['qty'] ?></td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                            <td>
                                <a href="transaksi.php?hapus=<?= $i ?>" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- PEMBAYARAN -->
<div class="card shadow-sm">
    <div class="card-body">

        <h5>Total Pembayaran</h5>
        <h3 class="fw-bold">Rp <?= number_format($total, 0, ',', '.') ?></h3>

        <form action="transaksi_add.php" method="POST" class="mt-3">

            <input type="hidden" name="total" value="<?= $total ?>">

            <label class="form-label">Pembayaran (Tunai)</label>
            <input type="number" class="form-control mb-3" name="pembayaran" required>

            <button class="btn btn-success w-100" <?= $total == 0 ? "disabled" : "" ?>>
                <i class="bi bi-check-circle"></i> Selesaikan Transaksi
            </button>

        </form>

    </div>
</div>

<?php if (isset($_SESSION['transaksi_sukses'])): ?>

    <!-- Bootstrap Success Modal -->
    <div class="modal fade show" id="modalTransaksi" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Transaksi Berhasil</h5>
                </div>

                <div class="modal-body">
                    <p class="mb-1"><b>Total:</b> Rp <?= number_format($_SESSION['transaksi_sukses']['total']) ?></p>
                    <p><b>Kembalian:</b> Rp <?= number_format($_SESSION['transaksi_sukses']['kembali']) ?></p>
                </div>

                <div class="modal-footer">
                    <a href="transaksi.php" class="btn btn-success">OK</a>
                </div>

            </div>
        </div>
    </div>

    <?php
    unset($_SESSION['transaksi_sukses']); // hapus setelah tampil
endif;
?>


<?php
$content = ob_get_clean();
include "layout.php";
