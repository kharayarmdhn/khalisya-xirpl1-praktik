<?php
include "koneksi.php";
include "navbar.php";

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST['aksi'] ?? '';
    $id_user = mysqli_real_escape_string($koneksi, trim($_POST['id_user']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $pass = (int)$_POST['pass'];
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));

    if ($aksi === "tambah") {
        $sql = "INSERT INTO tb_user (id_user, username, pass, role, nama) VALUES ('$id_user', '$username', $pass, '$role', '$nama')";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Data user berhasil ditambahkan.";
        } else {
            $pesan = "Gagal tambah user: " . mysqli_error($koneksi);
        }
    } elseif ($aksi === "update") {
        $sql = "UPDATE tb_user SET username = '$username', pass = $pass, role = '$role', nama = '$nama' WHERE id_user = '$id_user'";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Data user berhasil diupdate.";
        } else {
            $pesan = "Gagal update user: " . mysqli_error($koneksi);
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM tb_user WHERE id_user = '$id_hapus'")) {
        $pesan = "Data user berhasil dihapus.";
    } else {
        $pesan = "Gagal hapus user: " . mysqli_error($koneksi);
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $q_edit = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$id_edit' LIMIT 1");
    if ($q_edit && mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

$where = "";
if ($cari !== "") {
    $c = mysqli_real_escape_string($koneksi, $cari);
    $where = "WHERE id_user LIKE '%$c%' OR username LIKE '%$c%' OR role LIKE '%$c%' OR nama LIKE '%$c%'";
}
$data_user = mysqli_query($koneksi, "SELECT * FROM tb_user $where ORDER BY id_user ASC");
?>
<div class="container">
    <h3 class="mb-3">CRUD User</h3>

    <?php if ($pesan !== ""): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($pesan); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    <?= $edit_data ? "Edit User" : "Tambah User"; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="aksi" value="<?= $edit_data ? "update" : "tambah"; ?>">
                        <div class="mb-3">
                            <label class="form-label">ID User</label>
                            <input type="text" name="id_user" class="form-control" value="<?= htmlspecialchars($edit_data['id_user'] ?? ''); ?>" <?= $edit_data ? "readonly" : "required"; ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($edit_data['username'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (Angka)</label>
                            <input type="number" name="pass" class="form-control" value="<?= htmlspecialchars($edit_data['pass'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?= (($edit_data['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>admin</option>
                                <option value="customer" <?= (($edit_data['role'] ?? '') === 'customer') ? 'selected' : ''; ?>>customer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($edit_data['nama'] ?? ''); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= $edit_data ? "Simpan Perubahan" : "Simpan"; ?></button>
                        <?php if ($edit_data): ?>
                            <a href="crud_user.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Daftar User</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Role</th>
                                <th>Nama</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($data_user)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_user']); ?></td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['pass']); ?></td>
                                    <td><?= htmlspecialchars($row['role']); ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td>
                                        <a href="crud_user.php?edit=<?= urlencode($row['id_user']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="crud_user.php?hapus=<?= urlencode($row['id_user']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>