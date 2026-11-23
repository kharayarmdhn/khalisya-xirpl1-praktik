<?php
include "koneksi.php";
include "navbar.php";

$pesan = "";
$error = "";

$q_tiket = mysqli_query($koneksi, "SELECT * FROM tb_tiket ORDER BY kategori ASC");
$tiket_by_kategori = [];
$semua_tiket = [];
while ($t = mysqli_fetch_assoc($q_tiket)) {
    $semua_tiket[] = $t;
    $tiket_by_kategori[$t['kategori']] = $t;
}

$q_user = mysqli_query($koneksi, "SELECT id_user, nama, username FROM tb_user WHERE role = 'customer' ORDER BY id_user ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori'] ?? 'umum');
    $jml_tiket = max(1, (int)($_POST['jml_tiket'] ?? 1));
    $id_user = !empty($_SESSION['id_user']) ? mysqli_real_escape_string($koneksi, $_SESSION['id_user']) : mysqli_real_escape_string($koneksi, $_POST['id_user'] ?? '');

    if ($id_user === "") {
        $error = "Pilih user customer terlebih dahulu atau login.";
    } elseif (!isset($tiket_by_kategori[$kategori])) {
        $error = "Kategori tiket tidak tersedia di database.";
    } else {
        $tiket_dipilih = $tiket_by_kategori[$kategori];
        $id_tiket = mysqli_real_escape_string($koneksi, $tiket_dipilih['id_tiket']);
        $harga_satuan = (int)$tiket_dipilih['harga'];
        $total_harga = $harga_satuan * $jml_tiket;
        $id_transaksi = "TRX" . date("ymdHis") . rand(10, 99);

        $sql = "INSERT INTO tb_transaksi (id_transaksi, id_user, id_tiket, jml_tiket, total_harga) VALUES ('$id_transaksi', '$id_user', '$id_tiket', $jml_tiket, $total_harga)";
        if (mysqli_query($koneksi, $sql)) {
            $pesan = "Pembelian berhasil! ID Transaksi: $id_transaksi | Total: Rp " . number_format($total_harga, 0, ',', '.');
        } else {
            $error = "Gagal menyimpan transaksi: " . mysqli_error($koneksi);
        }
    }
}
?>
<div class="container">
    <h3 class="mb-3">Beli Tiket Kolam Renang</h3>

    <?php if ($pesan !== ""): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($pesan); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Form Pembelian Tiket</div>
                <div class="card-body">
                    <form method="POST">
                        <?php if (empty($_SESSION['id_user'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Pilih Customer</label>
                                <select name="id_user" class="form-select" required>
                                    <?php while ($u = mysqli_fetch_assoc($q_user)): ?>
                                        <option value="<?= htmlspecialchars($u['id_user']); ?>">
                                            <?= htmlspecialchars($u['nama']); ?> (<?= htmlspecialchars($u['username']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label d-block">Kategori Tiket</label>
                            <?php foreach ($semua_tiket as $idx => $t): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input kategori-radio" type="radio" name="kategori" id="kat_<?= htmlspecialchars($t['kategori']); ?>" value="<?= htmlspecialchars($t['kategori']); ?>" data-harga="<?= (int)$t['harga']; ?>" <?= $idx === 0 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="kat_<?= htmlspecialchars($t['kategori']); ?>">
                                        <?= htmlspecialchars(ucfirst($t['kategori'])); ?> (Rp <?= number_format((int)$t['harga'], 0, ',', '.'); ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Tiket</label>
                            <input type="number" name="jml_tiket" id="jml_tiket" min="1" value="1" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Harga</label>
                            <input type="text" id="tampil_total" class="form-control fw-bold" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Beli Tiket</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Daftar Harga Tiket</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Tiket</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($semua_tiket as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['id_tiket']); ?></td>
                                    <td><?= htmlspecialchars($t['kategori']); ?></td>
                                    <td>Rp <?= number_format((int)$t['harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const radios = document.querySelectorAll('.kategori-radio');
const jmlInput = document.getElementById('jml_tiket');
const totalInput = document.getElementById('tampil_total');

function hitungTotal() {
    let harga = 0;
    radios.forEach((r) => {
        if (r.checked) harga = parseInt(r.dataset.harga || 0, 10);
    });
    const jml = Math.max(1, parseInt(jmlInput.value || 1, 10));
    const total = harga * jml;
    totalInput.value = "Rp " + total.toLocaleString("id-ID");
}

radios.forEach((r) => r.addEventListener('change', hitungTotal));
jmlInput.addEventListener('input', hitungTotal);
hitungTotal();
</script>
</body>
</html>