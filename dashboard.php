<?php
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
<html>
<head>
<title>Dashboard - Supply Inventory</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#fafafb;
    font-family: 'Segoe UI', sans-serif;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
.topbar{
    background:#3e1f77;
    color:white;
    padding:12px 25px;
}
.brand{
    font-size:22px;
    font-weight:700;
    text-decoration:none;
    color:white;
}
.brand:hover{
    color:#ffd56a;
}
.hero{
    background:white;
    border-radius:20px;
    padding:40px;
    margin-top:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
.big-title{
    font-size:38px;
    font-weight:900;
    color:#2a1a4f;
}
.subtext{
    font-size:16px;
}
.stat-card{
    border-radius:18px;
    padding:25px;
    color:white;
}
.purple{background:#5a2bb5;}
.pink{background:#ff4d8b;}
.blue{background:#0096ff;}
.orange{background:#ff9f3f;}
.nav-btn{
    color:white;
    margin-right:12px;
    text-decoration:none;
    font-weight:500;
}
.nav-btn:hover{
    color:#ffd56a;
}
.module-card{
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
}
.feature-box{
    background:#ffffff;
    border-radius:20px;
    padding:30px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
}
.footer-section{
    background:#431c6e;
    color:white;
    margin-top:40px;
    padding:40px 20px;
    border-radius:25px;
}
.global-footer{
    background:#2a1a4f;
    color:white;
    padding:18px;
    margin-top:40px;
    text-align:center;
}
</style>
</head>
<body>

<div class="topbar d-flex justify-content-between align-items-center">
    <div>
        <a href="dashboard.php" class="brand">Supply Inventory</a>
    </div>

    <div>
        <?php if ($role=="Admin"): ?>
            <a class="nav-btn" href="dashboard.php?page=users">Users</a>
            <a class="nav-btn" href="dashboard.php?page=categories">Categories</a>
            <a class="nav-btn" href="dashboard.php?page=items">Items</a>
            <a class="nav-btn" href="dashboard.php?page=stock">Stock</a>
            <a class="nav-btn" href="dashboard.php?page=requests">Requests</a>
            <a class="nav-btn" href="dashboard.php?page=reports">Reports</a>
        <?php endif; ?>

        <?php if ($role=="Staff"): ?>
            <a class="nav-btn" href="dashboard.php?page=request_supply">Request Supplies</a>
            <a class="nav-btn" href="dashboard.php?page=view_stock">View Stock</a>
            <a class="nav-btn" href="dashboard.php?page=my_requests">My Requests</a>
        <?php endif; ?>

        <span class="ms-3 me-2"><?= $fullname ?> (<?= $role ?>)</span>
        <a href="logout.php" class="btn btn-warning btn-sm text-dark">Logout</a>
    </div>
</div>

<div class="container flex-grow-1">

<?php
if ($role=="Admin") {

    if ($page == 'home') {
        ?>
        <div class="hero">
            <div class="big-title">
                Centralized Supply and Inventory Management Dashboard
            </div>
            <p class="subtext mt-2">
                Manage users, stock, categories and supply requests in one system.
            </p>
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

            $total_users = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
            $total_categories = $conn->query("SELECT COUNT(*) as t FROM categories")->fetch_assoc()['t'];
            $total_items = $conn->query("SELECT COUNT(*) as t FROM items")->fetch_assoc()['t'];
            $pending_requests = $conn->query("SELECT COUNT(*) as t FROM requests WHERE status='Pending'")->fetch_assoc()['t'];
            ?>

            <div class="row mt-4 g-3">
                <div class="col-md-3">
                    <div class="stat-card purple">
                        <h6>Total Users</h6>
                        <h2><?= $total_users ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card blue">
                        <h6>Total Categories</h6>
                        <h2><?= $total_categories ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card pink">
                        <h6>Total Items</h6>
                        <h2><?= $total_items ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card orange">
                        <h6>Pending Requests</h6>
                        <h2><?= $pending_requests ?></h2>
                    </div>
                </div>
            </div>

            <h4 class="mt-5 mb-3 fw-bold">System Highlights</h4>

            <div class="row mt-2 g-4">
                <div class="col-md-3">
                    <div class="feature-box">
                        <h5>Multi-Department</h5>
                        <p>Supports multiple offices and units.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <h5>Live Monitoring</h5>
                        <p>Updated stock and request status.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <h5>Organized Records</h5>
                        <p>Structured categories and items list.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <h5>Report Ready</h5>
                        <p>Easily generate inventory reports.</p>
                    </div>
                </div>
            </div>

            <div class="footer-section mt-4 text-center">
                <h4 class="fw-bold">Supply Inventory Management System</h4>
                <p>Efficient • Modern • User-Friendly</p>
            </div>

            <?php
    }
}

if ($role=="Staff") {

    if ($page == 'home') {
        ?>
        <div class="hero">
            <div class="big-title">
                Centralized Supply and Inventory Management Dashboard
            </div>
            <p class="subtext mt-2">
                Request and monitor office supplies in one platform.
            </p>
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
                <div class="col-md-4">
                    <div class="card module-card p-3 text-center">
                        <h5>Request Supplies</h5>
                        <p>Create new supply request</p>
                        <a href="dashboard.php?page=request_supply" class="btn btn-primary btn-sm">Open</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card module-card p-3 text-center">
                        <h5>View Stock</h5>
                        <p>Check available stock</p>
                        <a href="dashboard.php?page=view_stock" class="btn btn-primary btn-sm">Open</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card module-card p-3 text-center">
                        <h5>My Requests</h5>
                        <p>View your submitted requests</p>
                        <a href="dashboard.php?page=my_requests" class="btn btn-primary btn-sm">Open</a>
                    </div>
                </div>
            </div>

            <h4 class="mt-5 mb-3 fw-bold">Why use this system?</h4>

            <div class="row mt-2 g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <h5>Fast Requesting</h5>
                        <p>Submit requests in seconds.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <h5>Transparent Status</h5>
                        <p>Track approval and releasing.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <h5>Organized History</h5>
                        <p>View all your past requests.</p>
                    </div>
                </div>
            </div>

            <div class="footer-section mt-4 text-center">
                <h4 class="fw-bold">Empowering Staff Request Process</h4>
                <p>Simple • Fast • Reliable</p>
            </div>

            <?php
    }
}
?>

</div>

<div class="global-footer">
    <small>© <?= date('Y'); ?> Centralized Supply and Inventory Management System • All Rights Reserved</small>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
