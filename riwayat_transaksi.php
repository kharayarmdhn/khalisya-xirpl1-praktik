<?php
include "koneksi.php";
include "navbar.php";

$where = "";
$conditions = [];

if (!empty($_SESSION['id_user']) && ($_SESSION['role'] ?? '') === 'customer') {
    $uid = mysqli_real_escape_string($koneksi, $_SESSION['id_user']);
    $conditions[] = "tr.id_user = '$uid'";
}

if ($cari !== "") {
    $c = mysqli_real_escape_string($koneksi, $cari);
    $conditions[] = "(tr.id_transaksi LIKE '%$c%' OR tk.kategori LIKE '%$c%' OR u.nama LIKE '%$c%')";
}

if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$sql = "SELECT tr.*, u.nama, tk.kategori, tk.harga
        FROM tb_transaksi tr
        JOIN tb_user u ON tr.id_user = u.id_user
        JOIN tb_tiket tk ON tr.id_tiket = tk.id_tiket
        $where
        ORDER BY tr.id_transaksi DESC";
$res = mysqli_query($koneksi, $sql);
?>
<div class="container">
    <h3 class="mb-3">Riwayat Transaksi</h3>
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id_transaksi']); ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['kategori']); ?></td>
                            <td>Rp <?= number_format((int)$row['harga'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['jml_tiket']); ?></td>
                            <td>Rp <?= number_format((int)$row['total_harga'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>