<?php
// Proteksi Kasir
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

if (!isset($page_title)) $page_title = "Kasir Panel";
if (!isset($content)) $content = "";
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $page_title ?> | Kedai Kopi 69</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f7f3ee; /* warna cream kopi */
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        #sidebar {
            width: 240px;
            background-color: #3b3024; /* Espresso */
            color: #fff;
            padding: 1rem;
            transition: margin-left .3s ease;
        }

        #sidebar.collapsed {
            margin-left: -240px;
        }

        #sidebar .nav-link {
            color: #f7f3ee;
            border-radius: .375rem;
            margin-bottom: .35rem;
        }

        #sidebar .nav-link.active,
        #sidebar .nav-link:hover {
            background-color: #8b6f47; /* Cappuccino */
            color: #fff;
        }

        #main-content {
            flex: 1;
            padding: 1.5rem;
        }

        @media(max-width:768px) {
            #sidebar {
                position: fixed;
                top: 56px;
                height: calc(100vh - 56px);
                z-index: 1030;
            }
        }
    </style>
</head>

<body>

    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-dark" style="background-color: #4b3a2f;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand ms-2 fw-bold" href="#">
                Kedai Kopi 69 – Kasir Panel
            </a>

            <span class="text-white ms-auto me-3">
                👋 Halo, <b><?= $_SESSION['nama'] ?></b>
            </span>
        </div>
    </nav>

    <!-- MAIN WRAPPER -->
    <div class="wrapper">

        <!-- SIDEBAR -->
        <div id="sidebar">
            <h6 class="text-uppercase fw-bold mb-3">Menu Kasir</h6>

            <ul class="nav nav-pills flex-column mb-auto">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>

                <li>
                    <a href="transaksi.php"
                       class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'transaksi.php' ? 'active' : '' ?>">
                        <i class="bi bi-receipt-cutoff me-2"></i> Transaksi Penjualan
                    </a>
                </li>

                <li>
                    <a href="riwayat.php"
                       class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? 'active' : '' ?>">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
                    </a>
                </li>

            </ul>

            <hr class="border-light">

            <a href="../logout.php" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>

        <!-- MAIN CONTENT -->
        <div id="main-content">
            <?= $content ?>
        </div>

    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sidebarToggle').onclick = () =>
            document.getElementById('sidebar').classList.toggle('collapsed');
    </script>

</body>
</html>
