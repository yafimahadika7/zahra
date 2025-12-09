<?php
// Judul halaman
$page_title = "Manajemen User";

// Koneksi DB
require_once "../koneksi/koneksi.php";

// ------------ HANDLE HAPUS USER ------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id_user = '$id'");
    header("Location: users.php");
    exit;
}

// ------------ HANDLE TAMBAH USER ------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $username, $password, $role);
    $stmt->execute();

    header("Location: users.php");
    exit;
}

// ------------ GET DATA USER ------------
$data_users = $conn->query("SELECT * FROM users ORDER BY id_user DESC");

// Mulai buffer untuk layout
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Manajemen User</h3>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="bi bi-person-plus me-1"></i> Tambah User
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $data_users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><span class="badge bg-secondary"><?= $row['role'] ?></span></td>
                            <td>
                                <!-- Tombol Edit -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEditUser<?= $row['id_user'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Tombol Hapus -->
                                <a href="users.php?delete=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <!-- MODAL EDIT USER -->
                        <div class="modal fade" id="modalEditUser<?= $row['id_user'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header" style="background:#8b6f47; color:white;">
                                        <h5 class="modal-title">Edit User - <?= $row['nama'] ?></h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>

                                    <form method="POST">
                                        <input type="hidden" name="edit_id" value="<?= $row['id_user'] ?>">

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="edit_nama" class="form-control"
                                                    value="<?= $row['nama'] ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="edit_username" class="form-control"
                                                    value="<?= $row['username'] ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Password Baru (Opsional)
                                                </label>
                                                <input type="password" name="edit_password" class="form-control"
                                                    placeholder="Biarkan kosong jika tidak ingin mengubah password">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select name="edit_role" class="form-select" required>
                                                    <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>
                                                        Admin</option>
                                                    <option value="kasir" <?= $row['role'] == 'kasir' ? 'selected' : '' ?>>
                                                        Kasir</option>
                                                    <option value="owner" <?= $row['role'] == 'owner' ? 'selected' : '' ?>>
                                                        Owner</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background:#4b3a2f; color:white;">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php
// Akhiri buffer, kirim ke layout
$content = ob_get_clean();
include "layout.php";
?>