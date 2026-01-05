<?php
ob_start(); 
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$fullname = $_SESSION['fullname'];

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Supply Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            background:#fafafb; 
            font-family: 'Segoe UI', sans-serif; 
            min-height:100vh; 
            display:flex; 
            flex-direction:column; 
        }
        
        .topbar { 
            background:#3e1f77; 
            color:white; 
            padding:10px 25px; 
            position: sticky; 
            top: 0; 
            z-index: 1020; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .brand { 
            font-size:22px; 
            font-weight:700; 
            text-decoration:none; 
            color:white; 
        }
        .brand:hover { 
            color:#ffd56a; 
        }

        .offcanvas { 
            background-color: #2a1a4f; 
            color: white; 
            width: 280px !important; 
        }
        .offcanvas-header { 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        .drawer-link { 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            padding: 12px 20px; 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            border-radius: 8px; 
            transition: 0.3s; 
            margin: 4px 15px; 
        }
        .drawer-link:hover, .drawer-link.active { 
            background: #5a2bb5; 
            color: #ffd56a; 
        }
        
        .hero { 
            background:white; 
            border-radius:20px; 
            padding:40px; 
            margin-top:25px; 
            box-shadow:0 10px 25px rgba(0,0,0,0.08); 
        }
        .big-title { 
            font-size:32px; 
            font-weight:900; 
            color:#2a1a4f; 
        }
        .stat-card { 
            border-radius:18px; 
            padding:25px; 
            color:white; 
        }
        .purple { 
            background:#5a2bb5; 
        } 
        .pink { 
            background:#ff4d8b; 
        } 
        .blue { 
            background:#0096ff; 
        } 
        .orange { 
            background:#ff9f3f; 
        }
        .module-card { 
            border-radius:16px; 
            box-shadow:0 8px 20px rgba(0,0,0,0.05); 
        }
        .feature-box { 
            background:#ffffff; 
            border-radius:20px; 
            padding:30px; 
            text-align:center; 
            box-shadow:0 8px 20px rgba(0,0,0,0.05); 
        }
        .footer-section { 
            background:#431c6e; 
            color:white; 
            margin-top:40px; 
            padding:40px 20px; 
            border-radius:25px; 
        }
        .global-footer { 
            background:#2a1a4f; 
            color:white; 
            padding:18px; 
            margin-top:40px; 
            text-align:center; 
        }

        @media (max-width: 768px) { .big-title { font-size: 24px; } }
    </style>
</head>
<body>

<div class="topbar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <button class="btn text-white p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#navDrawer">
            <i class="bi bi-list fs-2"></i>
        </button>
        <a href="dashboard.php" class="brand">Supply Inventory</a>
    </div>

    <div class="d-flex align-items-center">
        <span class="d-none d-md-inline ms-3 me-3 opacity-75 small fw-bold"><?= $fullname ?> (<?= $role ?>)</span>
        <button onclick="confirmLogout()" class="btn btn-warning btn-sm text-dark fw-bold px-3">Logout</button>
    </div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="navDrawer" aria-labelledby="navDrawerLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="navDrawerLabel">Menu Navigation</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 pt-3">
        <?php if ($role=="Admin"): ?>
            <a class="drawer-link <?= $page=='home'?'active':'' ?>" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a>
            <a class="drawer-link <?= $page=='users'?'active':'' ?>" href="dashboard.php?page=users"><i class="bi bi-people-fill"></i> Users</a>
            <a class="drawer-link <?= $page=='categories'?'active':'' ?>" href="dashboard.php?page=categories"><i class="bi bi-tags-fill"></i> Categories</a>
            <a class="drawer-link <?= $page=='items'?'active':'' ?>" href="dashboard.php?page=items"><i class="bi bi-box-fill"></i> Items</a>
            <a class="drawer-link <?= $page=='stock'?'active':'' ?>" href="dashboard.php?page=stock"><i class="bi bi-stack"></i> Stock</a>
            <a class="drawer-link <?= $page=='requests'?'active':'' ?>" href="dashboard.php?page=requests"><i class="bi bi-card-list"></i> Requests</a>
            <a class="drawer-link <?= $page=='reports'?'active':'' ?>" href="dashboard.php?page=reports"><i class="bi bi-bar-chart-fill"></i> Reports</a>
        <?php endif; ?>

        <?php if ($role=="Staff"): ?>
            <a class="drawer-link <?= $page=='home'?'active':'' ?>" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a>
            <a class="drawer-link <?= $page=='request_supply'?'active':'' ?>" href="dashboard.php?page=request_supply"><i class="bi bi-pencil-square"></i> Request Supplies</a>
            <a class="drawer-link <?= $page=='view_stock'?'active':'' ?>" href="dashboard.php?page=view_stock"><i class="bi bi-box"></i> View Stock</a>
            <a class="drawer-link <?= $page=='my_requests'?'active':'' ?>" href="dashboard.php?page=my_requests"><i class="bi bi-card-checklist"></i> My Requests</a>
        <?php endif; ?>
    </div>
</div>

<div class="container flex-grow-1">
<?php
if ($role=="Admin") {
    if ($page == 'home') {
        ?>
        <div class="hero">
            <div class="big-title">Centralized Supply and Inventory Management Dashboard</div>
            <p class="subtext mt-2">Manage users, stock, categories and supply requests in one system.</p>
        </div>
        <?php
    }

    switch($page){
        case 'users':
        case 'categories':
        case 'items':
        case 'stock':
        case 'requests':
        case 'reports':
            include "admin/{$page}.php";
            break;
        default:
            // FETCH DASHBOARD STATS
            $total_users = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
            $total_categories = $conn->query("SELECT COUNT(*) as t FROM categories")->fetch_assoc()['t'];
            $total_items = $conn->query("SELECT COUNT(*) as t FROM items")->fetch_assoc()['t'];
            $pending_requests = $conn->query("SELECT COUNT(*) as t FROM requests WHERE status='Pending'")->fetch_assoc()['t'];
            ?>
            <div class="row mt-4 g-3">
                <div class="col-md-3"><div class="stat-card purple d-flex justify-content-between align-items-center"><div><h6>Total Users</h6><h2><?= $total_users ?></h2></div><i class="bi bi-people-fill fs-1 opacity-75"></i></div></div>
                <div class="col-md-3"><div class="stat-card blue d-flex justify-content-between align-items-center"><div><h6>Total Categories</h6><h2><?= $total_categories ?></h2></div><i class="bi bi-tags-fill fs-1 opacity-75"></i></div></div>
                <div class="col-md-3"><div class="stat-card pink d-flex justify-content-between align-items-center"><div><h6>Total Items</h6><h2><?= $total_items ?></h2></div><i class="bi bi-box-seam fs-1 opacity-75"></i></div></div>
                <div class="col-md-3"><div class="stat-card orange d-flex justify-content-between align-items-center"><div><h6>Pending Requests</h6><h2><?= $pending_requests ?></h2></div><i class="bi bi-hourglass-split fs-1 opacity-75"></i></div></div>
            </div>
            <h4 class="mt-5 mb-3 fw-bold">System Highlights</h4>
            <div class="row mt-2 g-4">
                <div class="col-md-3 text-center"><div class="feature-box"><h5>Multi-Department</h5><p class="small text-muted">Supports multiple offices and units.</p></div></div>
                <div class="col-md-3 text-center"><div class="feature-box"><h5>Live Monitoring</h5><p class="small text-muted">Updated stock and request status.</p></div></div>
                <div class="col-md-3 text-center"><div class="feature-box"><h5>Organized Records</h5><p class="small text-muted">Structured categories and items list.</p></div></div>
                <div class="col-md-3 text-center"><div class="feature-box"><h5>Report Ready</h5><p class="small text-muted">Easily generate inventory reports.</p></div></div>
            </div>
            <?php
    }
}

if ($role=="Staff") {
    if ($page == 'home') {
        ?>
        <div class="hero">
            <div class="big-title">Centralized Supply and Inventory Management Dashboard</div>
            <p class="subtext mt-2">Request and monitor office supplies in one platform.</p>
        </div>
        <?php
    }

    switch($page){
        case 'request_supply':
        case 'view_stock':
        case 'my_requests':
            include "staff/{$page}.php";
            break;
        default:
            ?>
            <div class="row g-4 mt-4">
                <div class="col-md-4"><div class="card module-card p-4 text-center h-100"><div class="mb-3"><i class="bi bi-pencil-square fs-1 text-primary"></i></div><h5 class="fw-bold">Request Supplies</h5><p class="small">Create new supply request</p><a href="dashboard.php?page=request_supply" class="btn btn-primary btn-sm mt-auto">Open</a></div></div>
                <div class="col-md-4"><div class="card module-card p-4 text-center h-100"><div class="mb-3"><i class="bi bi-box-seam fs-1 text-success"></i></div><h5 class="fw-bold">View Stock</h5><p class="small">Check available stock</p><a href="dashboard.php?page=view_stock" class="btn btn-primary btn-sm mt-auto">Open</a></div></div>
                <div class="col-md-4"><div class="card module-card p-4 text-center h-100"><div class="mb-3"><i class="bi bi-card-checklist fs-1 text-warning"></i></div><h5 class="fw-bold">My Requests</h5><p class="small">View your submitted requests</p><a href="dashboard.php?page=my_requests" class="btn btn-primary btn-sm mt-auto">Open</a></div></div>
            </div>
            <?php
    }
}
?>
</div>

<div class="global-footer">
    <small>© <?= date('Y'); ?> Supply Inventory Management System • All Rights Reserved</small>
</div>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Logout Confirmation',
        text: "Are you sure you want to end your current session?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3e1f77', // Matches topbar theme
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>