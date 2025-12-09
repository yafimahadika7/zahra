<?php
$page_title = "Menu Kopi";
require_once "../koneksi/koneksi.php";


// ----------------------------------------------------
// HANDLE TAMBAH MENU
// ----------------------------------------------------
if (isset($_POST['tambah_menu'])) {

    $nama = $_POST['nama_menu'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];

    $stmt = $conn->prepare("INSERT INTO menu (nama_menu, kategori, harga) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama, $kategori, $harga);
    $stmt->execute();

    header("Location: menu.php");
    exit;
}


// ----------------------------------------------------
// HANDLE EDIT MENU
// ----------------------------------------------------
if (isset($_POST['edit_menu'])) {

    $id = $_POST['id_menu'];
    $nama = $_POST['edit_nama_menu'];
    $kategori = $_POST['edit_kategori'];
    $harga = $_POST['edit_harga'];

    $stmt = $conn->prepare("UPDATE menu SET nama_menu=?, kategori=?, harga=? WHERE id_menu=?");
    $stmt->bind_param("ssii", $nama, $kategori, $harga, $id);
    $stmt->execute();

    header("Location: menu.php");
    exit;
}


// ----------------------------------------------------
// HANDLE DELETE MENU
// ----------------------------------------------------
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM menu WHERE id_menu = '$del_id'");
    header("Location: menu.php");
    exit;
}


// ----------------------------------------------------
// GET DATA MENU
// ----------------------------------------------------
$menus = $conn->query("SELECT * FROM menu ORDER BY id_menu DESC");


// MULAI BUFFER KONTEN
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Daftar Menu Kopi</h3>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMenu">
        <i class="bi bi-plus-circle me-1"></i> Tambah Menu
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1;
                    while ($row = $menus->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>

                            <td>

                                <!-- BUTTON EDIT -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEditMenu<?= $row['id_menu'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- BUTTON DELETE -->
                                <a href="menu.php?delete=<?= $row['id_menu'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus menu ini?')"
                                    class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>

                            </td>
                        </tr>


                        <!-- MODAL EDIT MENU -->
                        <div class="modal fade" id="modalEditMenu<?= $row['id_menu'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header" style="background:#8b6f47; color:white;">
                                        <h5 class="modal-title">Edit Menu Kopi</h5>
                                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form method="POST">
                                        <input type="hidden" name="id_menu" value="<?= $row['id_menu'] ?>">

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">Nama Menu</label>
                                                <input type="text" name="edit_nama_menu" class="form-control"
                                                    value="<?= htmlspecialchars($row['nama_menu']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kategori</label>
                                                <input type="text" name="edit_kategori" class="form-control"
                                                    value="<?= htmlspecialchars($row['kategori']) ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Harga</label>
                                                <input type="number" name="edit_harga" class="form-control"
                                                    value="<?= $row['harga'] ?>" required>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Tutup
                                            </button>
                                            <button type="submit" name="edit_menu" class="btn btn-primary">
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


<!-- MODAL TAMBAH MENU -->
<div class="modal fade" id="modalTambahMenu" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background:#4b3a2f; color:white;">
                <h5 class="modal-title">Tambah Menu Kopi</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="tambah_menu" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>


<?php
// OUTPUT ke layout
$content = ob_get_clean();
include "layout.php";
?>