<?php
include "koneksi.php";
include "navbar.php";

$pesan = "";
$list_user = mysqli_query($koneksi, "SELECT id_user, username, nama FROM tb_user ORDER BY id_user ASC");
$list_tiket = mysqli_query($koneksi, "SELECT id_tiket, kategori, harga FROM tb_tiket ORDER BY id_tiket ASC");

$harga_map = [];
mysqli_data_seek($list_tiket, 0);
while ($t = mysqli_fetch_assoc($list_tiket)) {
    $harga_map[$t['id_tiket']] = (int)$t['harga'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST['aksi'] ?? '';
    $id_transaksi = mysqli_real_escape_string($koneksi, trim($_POST['id_transaksi']));
    $id_user = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $id_tiket = mysqli_real_escape_string($koneksi, $_POST['id_tiket']);
    $jml_tiket = max(1, (int)$_POST['jml_tiket']);
    $harga_satuan = $harga_map[$id_tiket] ?? 0;
    $total_harga = $harga_satuan * $jml_tiket;

    if ($aksi === "tambah") {
        $sql = "INSERT INTO tb_transaksi (id_transaksi, id_user, id_tiket, jml_tiket, total_harga) VALUES ('$id_transaksi', '$id_user', '$id_tiket', $jml_tiket, $total_harga)";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Transaksi berhasil ditambahkan.";
        } else {
            $pesan = "Gagal tambah transaksi: " . mysqli_error($koneksi);
        }
    } elseif ($aksi === "update") {
        $sql = "UPDATE tb_transaksi SET id_user = '$id_user', id_tiket = '$id_tiket', jml_tiket = $jml_tiket, total_harga = $total_harga WHERE id_transaksi = '$id_transaksi'";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Transaksi berhasil diupdate.";
        } else {
            $pesan = "Gagal update transaksi: " . mysqli_error($koneksi);
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM tb_transaksi WHERE id_transaksi = '$id_hapus'")) {
        $pesan = "Transaksi berhasil dihapus.";
    } else {
        $pesan = "Gagal hapus transaksi: " . mysqli_error($koneksi);
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $q_edit = mysqli_query($koneksi, "SELECT * FROM tb_transaksi WHERE id_transaksi = '$id_edit' LIMIT 1");
    if ($q_edit && mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

$where = "";
if ($cari !== "") {
    $c = mysqli_real_escape_string($koneksi, $cari);
    $where = "WHERE tr.id_transaksi LIKE '%$c%' OR u.nama LIKE '%$c%' OR tk.kategori LIKE '%$c%'";
}

$sql_tampil = "SELECT tr.*, u.nama, u.username, tk.kategori, tk.harga
               FROM tb_transaksi tr
               JOIN tb_user u ON tr.id_user = u.id_user
               JOIN tb_tiket tk ON tr.id_tiket = tk.id_tiket
               $where
               ORDER BY tr.id_transaksi ASC";
$data_transaksi = mysqli_query($koneksi, $sql_tampil);
?>
<div class="container">
    <h3 class="mb-3">CRUD Transaksi</h3>

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
                    <?= $edit_data ? "Edit Transaksi" : "Tambah Transaksi"; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="aksi" value="<?= $edit_data ? "update" : "tambah"; ?>">
                        <div class="mb-3">
                            <label class="form-label">ID Transaksi</label>
                            <input type="text" name="id_transaksi" class="form-control" value="<?= htmlspecialchars($edit_data['id_transaksi'] ?? ''); ?>" <?= $edit_data ? "readonly" : "required"; ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="id_user" class="form-select" required>
                                <?php mysqli_data_seek($list_user, 0); while ($u = mysqli_fetch_assoc($list_user)): ?>
                                    <option value="<?= htmlspecialchars($u['id_user']); ?>" <?= (($edit_data['id_user'] ?? '') === $u['id_user']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($u['nama']); ?> (<?= htmlspecialchars($u['username']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiket</label>
                            <select name="id_tiket" class="form-select" required>
                                <?php mysqli_data_seek($list_tiket, 0); while ($t = mysqli_fetch_assoc($list_tiket)): ?>
                                    <option value="<?= htmlspecialchars($t['id_tiket']); ?>" <?= (($edit_data['id_tiket'] ?? '') === $t['id_tiket']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($t['id_tiket']); ?> - <?= htmlspecialchars($t['kategori']); ?> (Rp <?= number_format((int)$t['harga'], 0, ',', '.'); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Tiket</label>
                            <input type="number" name="jml_tiket" min="1" class="form-control" value="<?= htmlspecialchars($edit_data['jml_tiket'] ?? '1'); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= $edit_data ? "Simpan Perubahan" : "Simpan"; ?></button>
                        <?php if ($edit_data): ?>
                            <a href="crud_transaksi.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Daftar Transaksi</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($data_transaksi)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_transaksi']); ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><?= htmlspecialchars($row['kategori']); ?></td>
                                    <td><?= htmlspecialchars($row['jml_tiket']); ?></td>
                                    <td>Rp <?= number_format((int)$row['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <a href="crud_transaksi.php?edit=<?= urlencode($row['id_transaksi']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="crud_transaksi.php?hapus=<?= urlencode($row['id_transaksi']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus transaksi ini?')">Hapus</a>
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