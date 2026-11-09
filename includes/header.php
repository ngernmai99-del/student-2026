<?php
require_once __DIR__ . '/../config/db.php';
$currentUser = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$sysSettings = getSystemSettings($pdo);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'ລະບົບອອກໃບຢັ້ງຢືນຈົບການສຶກສາ ລໍຖ້າຮັບໃບປະກາດ') ?></title>
    <!-- Google Fonts: Noto Sans Lao -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-navy: #0f2942;
            --secondary-blue: #1a4f8b;
            --accent-gold: #d4a017;
            --accent-gold-light: #fef9e7;
            --bg-soft: #f4f7fb;
        }

        body {
            font-family: 'Noto Sans Lao', 'Phetsarath OT', 'Saysettha OT', sans-serif;
            background-color: var(--bg-soft);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar-gov {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--secondary-blue) 100%);
            border-bottom: 4px solid var(--accent-gold);
            box-shadow: 0 4px 15px rgba(15, 41, 66, 0.15);
        }

        .brand-emblem {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd700, #c59100);
            color: var(--primary-navy);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            flex-shrink: 0;
        }

        .nav-link-custom {
            color: rgba(255,255,255,0.88) !important;
            font-weight: 500;
            padding: 0.55rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            margin: 0 0.15rem;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.16);
        }

        .nav-link-custom.active {
            box-shadow: inset 0 -2px 0 var(--accent-gold);
        }

        /* Cards & Form Sections */
        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 6px 24px rgba(15, 41, 66, 0.07);
            background: #ffffff;
        }

        .card-header-gov {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--secondary-blue) 100%);
            color: #fff;
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
            padding: 1.15rem 1.5rem;
            border-bottom: 3px solid var(--accent-gold);
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background-color: #eef4fb;
            color: var(--secondary-blue);
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.45rem 0.9rem;
            border-radius: 0.5rem;
            border-left: 4px solid var(--secondary-blue);
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.92rem;
        }

        .form-control, .form-select {
            border-radius: 0.6rem;
            padding: 0.62rem 0.9rem;
            border-color: #cbd5e1;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 0.22rem rgba(26, 79, 139, 0.18);
        }

        /* Prefix Radio Pills */
        .prefix-selector .btn-check:checked + .btn-outline-primary {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
            color: #fff;
            box-shadow: 0 3px 10px rgba(26, 79, 139, 0.25);
        }

        .prefix-selector .btn-outline-primary {
            border-color: #cbd5e1;
            color: #334155;
            font-weight: 600;
            padding: 0.6rem 1rem;
            border-radius: 0.6rem;
        }

        /* Stat Cards */
        .stat-card {
            border: none;
            border-radius: 0.9rem;
            box-shadow: 0 4px 16px rgba(15, 41, 66, 0.06);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Table Styling */
        .table-gov thead th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.88rem;
            border-bottom: 2px solid #cbd5e1;
            white-space: nowrap;
            padding: 0.85rem 0.75rem;
        }

        .table-gov tbody td {
            vertical-align: middle;
            padding: 0.75rem;
            font-size: 0.92rem;
        }

        .badge-prefix-thao {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }
        .badge-prefix-nang {
            background-color: #fce7f3;
            color: #be185d;
            font-weight: 600;
        }
        .badge-prefix-sn {
            background-color: #fef3c7;
            color: #b45309;
            font-weight: 600;
        }
        .badge-prefix-khouba {
            background-color: #ffedd5;
            color: #c2410c;
            font-weight: 600;
        }

        .footer-gov {
            margin-top: auto;
            background-color: #0f2942;
            color: rgba(255,255,255,0.75);
            border-top: 3px solid var(--accent-gold);
        }

        .swal2-popup {
            font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif !important;
            border-radius: 1rem !important;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-gov sticky-top py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3" href="index.php">
            <div class="brand-emblem">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <div class="fw-bold fs-6 lh-sm text-white">ລະບົບອອກໃບຢັ້ງຢືນຈົບການສຶກສາ (ລໍຖ້າຮັບໃບປະກາດ)</div>
                <div class="small text-warning opacity-90" style="font-size: 0.78rem;">
                    <i class="bi bi-globe2 me-1"></i>ລະບົບອອນລາຍ | <?= htmlspecialchars($sysSettings['institution_name'] ?? '') ?>
                </div>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1 mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-pencil-square me-1"></i>ຟອມບັນທຶກຂໍ້ມູນ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $currentPage === 'report.php' ? 'active' : '' ?>" href="report.php">
                        <i class="bi bi-table me-1"></i>ຂໍ້ມູນລາຍງານ & Excel
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= $currentPage === 'certificate.php' ? 'active' : '' ?>" href="certificate.php">
                        <i class="bi bi-award-fill me-1"></i>ພິມໃບຢັ້ງຢືນ
                    </a>
                </li>

                <?php if ($currentUser): ?>
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="btn btn-warning btn-sm fw-semibold dropdown-toggle d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-check-fill"></i>
                            <span><?= htmlspecialchars($currentUser['full_name']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    ສະຖານະ: <strong class="text-success">ເຂົ້າສູ່ລະບົບແລ້ວ (ແກ້ໄຂຂໍ້ມູນໄດ້)</strong>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                    <i class="bi bi-gear-fill me-2 text-primary"></i>ຕັ້ງຄ່າຫົວໃບຢັ້ງຢືນ
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="bi bi-key-fill me-2 text-warning"></i>ປ່ຽນລະຫັດຜ່ານ Admin
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>ອອກຈາກລະບົບ (Logout)
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-warning btn-sm fw-bold px-3 py-2 rounded-pill shadow-sm" href="login.php">
                            <i class="bi bi-shield-lock-fill me-1"></i>ເຂົ້າສູ່ລະບົບ (ແກ້ໄຂຂໍ້ມູນ)
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

