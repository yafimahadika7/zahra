<?php
$page_title = "Stok Bahan";
require_once "../koneksi/koneksi.php";

// -------------------- HANDLE TAMBAH -----------------------
if (isset($_POST['tambah_stok'])) {
    $nama = $_POST['nama_bahan'];
    $satuan = $_POST['satuan'];
    $jumlah = $_POST['jumlah'];

    $stmt = $conn->prepare("INSERT INTO stok (nama_bahan, satuan, jumlah) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama, $satuan, $jumlah);
    $stmt->execute();

    header("Location: stok.php");
    exit;
}

// -------------------- HANDLE EDIT -------------------------
if (isset($_POST['edit_stok'])) {
    $id = $_POST['id_stok'];
    $nama = $_POST['edit_nama_bahan'];
    $satuan = $_POST['edit_satuan'];
    $jumlah = $_POST['edit_jumlah'];

    $stmt = $conn->prepare("UPDATE stok SET nama_bahan=?, satuan=?, jumlah=? WHERE id_stok=?");
    $stmt->bind_param("ssii", $nama, $satuan, $jumlah, $id);
    $stmt->execute();

    header("Location: stok.php");
    exit;
}

// -------------------- HANDLE DELETE -----------------------
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM stok WHERE id_stok = '$delete_id'");
    header("Location: stok.php");
    exit;
}

// -------------------- GET DATA ----------------------------
$stok = $conn->query("SELECT * FROM stok ORDER BY id_stok DESC");

// Mulai buffer output
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Manajemen Stok Bahan</h3>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahStok">
        <i class="bi bi-plus-circle me-1"></i> Tambah Bahan
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th>Jumlah</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1;
                    while ($row = $stok->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_bahan']) ?></td>
                            <td><?= htmlspecialchars($row['satuan']) ?></td>
                            <td><?= $row['jumlah'] ?></td>

                            <td>
                                <!-- EDIT -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEditStok<?= $row['id_stok'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- DELETE -->
                                <a href="stok.php?delete=<?= $row['id_stok'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus bahan ini?')"
                                    class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="modalEditStok<?= $row['id_stok'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header" style="background:#8b6f47; color:white;">
                                        <h5 class="modal-title">Edit Stok Bahan</h5>
                                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form method="POST">
                                        <input type="hidden" name="id_stok" value="<?= $row['id_stok'] ?>">

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">Nama Bahan</label>
                                                <input type="text" name="edit_nama_bahan" class="form-control"
                                                    value="<?= htmlspecialchars($row['nama_bahan']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Satuan</label>
                                                <input type="text" name="edit_satuan" class="form-control"
                                                    value="<?= htmlspecialchars($row['satuan']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Jumlah</label>
                                                <input type="number" name="edit_jumlah" class="form-control"
                                                    value="<?= $row['jumlah'] ?>" required>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Tutup
                                            </button>
                                            <button type="submit" name="edit_stok" class="btn btn-primary">
                                                Simpan Perubahan
                                            </button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>


<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambahStok" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background:#4b3a2f; color:white;">
                <h5 class="modal-title">Tambah Stok Bahan</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="nama_bahan" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan (gram/ml/pcs)</label>
                        <input type="text" name="satuan" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="tambah_stok" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "layout.php";
?>