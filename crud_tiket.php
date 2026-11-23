<?php
include "koneksi.php";
include "navbar.php";

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST['aksi'] ?? '';
    $id_tiket = mysqli_real_escape_string($koneksi, trim($_POST['id_tiket']));
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = (int)$_POST['harga'];

    if ($aksi === "tambah") {
        $sql = "INSERT INTO tb_tiket (id_tiket, kategori, harga) VALUES ('$id_tiket', '$kategori', $harga)";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Data tiket berhasil ditambahkan.";
        } else {
            $pesan = "Gagal tambah tiket: " . mysqli_error($koneksi);
        }
    } elseif ($aksi === "update") {
        $sql = "UPDATE tb_tiket SET kategori = '$kategori', harga = $harga WHERE id_tiket = '$id_tiket'";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Data tiket berhasil diupdate.";
        } else {
            $pesan = "Gagal update tiket: " . mysqli_error($koneksi);
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM tb_tiket WHERE id_tiket = '$id_hapus'")) {
        $pesan = "Data tiket berhasil dihapus.";
    } else {
        $pesan = "Gagal hapus tiket: " . mysqli_error($koneksi);
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $q_edit = mysqli_query($koneksi, "SELECT * FROM tb_tiket WHERE id_tiket = '$id_edit' LIMIT 1");
    if ($q_edit && mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

$where = "";
if ($cari !== "") {
    $c = mysqli_real_escape_string($koneksi, $cari);
    $where = "WHERE id_tiket LIKE '%$c%' OR kategori LIKE '%$c%' OR CAST(harga AS CHAR) LIKE '%$c%'";
}
$data_tiket = mysqli_query($koneksi, "SELECT * FROM tb_tiket $where ORDER BY id_tiket ASC");
?>
<div class="container">
    <h3 class="mb-3">CRUD Tiket</h3>

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
                    <?= $edit_data ? "Edit Tiket" : "Tambah Tiket"; ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="aksi" value="<?= $edit_data ? "update" : "tambah"; ?>">
                        <div class="mb-3">
                            <label class="form-label">ID Tiket</label>
                            <input type="text" name="id_tiket" class="form-control" value="<?= htmlspecialchars($edit_data['id_tiket'] ?? ''); ?>" <?= $edit_data ? "readonly" : "required"; ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="umum" <?= (($edit_data['kategori'] ?? '') === 'umum') ? 'selected' : ''; ?>>umum</option>
                                <option value="membership" <?= (($edit_data['kategori'] ?? '') === 'membership') ? 'selected' : ''; ?>>membership</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($edit_data['harga'] ?? ''); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= $edit_data ? "Simpan Perubahan" : "Simpan"; ?></button>
                        <?php if ($edit_data): ?>
                            <a href="crud_tiket.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Daftar Tiket</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Tiket</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($data_tiket)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_tiket']); ?></td>
                                    <td><?= htmlspecialchars($row['kategori']); ?></td>
                                    <td>Rp <?= number_format((int)$row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <a href="crud_tiket.php?edit=<?= urlencode($row['id_tiket']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="crud_tiket.php?hapus=<?= urlencode($row['id_tiket']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
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