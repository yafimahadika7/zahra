<?php
// Proteksi Admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($page_title))
    $page_title = "Admin Panel";
if (!isset($content))
    $content = "";
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $page_title ?> | Kedai Kopi 69</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f7f3ee;
            /* warna cream kopi */
            font-family: "Poppins", sans-serif;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        #sidebar {
            width: 240px;
            background-color: #3b3024;
            /* espresso dark */
            color: #fff;
            padding: 1rem;
            transition: margin-left .3s ease;
        }

        #sidebar.collapsed {
            margin-left: -240px;
        }

        #sidebar h6 {
            letter-spacing: 1px;
        }

        #sidebar .nav-link {
            color: #f7f3ee;
            padding: 10px 12px;
            border-radius: .375rem;
            font-size: 15px;
        }

        #sidebar .nav-link.active,
        #sidebar .nav-link:hover {
            background-color: #8b6f47;
            /* cappuccino soft brown */
            color: #fff;
        }

        /* CONTENT AREA */
        #main-content {
            flex: 1;
            padding: 1.6rem;
        }

        /* NAVBAR */
        .navbar {
            background-color: #4b3a2f !important;
            /* dark chocolate */
        }

        .navbar-brand {
            font-weight: 600;
        }

        /* Responsive */
        @media(max-width: 768px) {
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

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark px-3">
        <button class="navbar-toggler" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand ms-2" href="#">
            Kedai Kopi 69 – Admin
        </a>

        <span class="text-white ms-auto">
            👋 Halo, <b><?= $_SESSION['nama'] ?></b>
        </span>
    </nav>

    <div class="wrapper">

        <!-- SIDEBAR -->
        <div id="sidebar">
            <h6 class="text-uppercase fw-bold mb-3">Menu Admin</h6>

            <ul class="nav flex-column mb-4">

                <li class="nav-item">
                    <a href="dashboard.php"
                        class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>

                <li>
                    <a href="menu.php"
                        class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : '' ?>">
                        <i class="bi bi-cup-hot me-2"></i> Menu Kopi
                    </a>
                </li>

                <li>
                    <a href="stok.php"
                        class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'stok.php' ? 'active' : '' ?>">
                        <i class="bi bi-box-seam me-2"></i> Stok Bahan
                    </a>
                </li>

                <li>
                    <a href="users.php"
                        class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                        <i class="bi bi-people-fill me-2"></i> Manajemen User
                    </a>
                </li>

            </ul>

            <a href="../logout.php" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>

        <!-- MAIN CONTENT -->
        <div id="main-content">
            <?= $content ?>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebarToggle').onclick = () =>
            document.getElementById('sidebar').classList.toggle('collapsed');
    </script>

</body>

</html>