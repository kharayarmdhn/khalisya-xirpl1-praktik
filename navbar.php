<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nama_user = !empty($_SESSION['nama']) ? $_SESSION['nama'] : (!empty($_SESSION['username']) ? $_SESSION['username'] : 'Tamu');
$role_user = !empty($_SESSION['role']) ? strtolower($_SESSION['role']) : 'guest';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Tiket Kolam Renang</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= ($role_user === 'customer') ? 'beli_tiket.php' : 'crud_tiket.php'; ?>">Tiket Kolam</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <?php if ($role_user === 'customer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="beli_tiket.php">Beli Tiket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat_transaksi.php">Riwayat Transaksi</a>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="crud_tiket.php">CRUD Tiket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="crud_user.php">CRUD User</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="crud_transaksi.php">CRUD Transaksi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="beli_tiket.php">Form Beli Tiket</a>
                        </li>
                    <?php endif; ?>

                </ul>

                <div class="d-flex align-items-center gap-3">
                    <form class="d-flex" role="search" method="GET">
                        <input class="form-control me-2" type="search" name="cari" placeholder="Search" aria-label="Search" value="<?= htmlspecialchars($_GET['cari'] ?? ''); ?>" />
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span><?= htmlspecialchars($nama_user); ?> ( <?= htmlspecialchars(ucfirst($role_user)); ?> )</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right me-2"></i>Login / Ganti Akun</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>