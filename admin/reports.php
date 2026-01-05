<?php
<<<<<<< HEAD
// ADMIN ONLY check
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger m-3'>Access denied.</div>";
=======
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
    exit;
}

// FILTER VALUES
$filter_cat = $_GET['category'] ?? "";
$from_date  = $_GET['from'] ?? "";
$to_date    = $_GET['to'] ?? "";
<<<<<<< HEAD

// INVENTORY QUERY
=======
$export     = $_GET['export'] ?? "";

// ======================
// INVENTORY QUERY
// ======================
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
$inv_sql = "SELECT items.*, categories.name AS cat_name
            FROM items
            JOIN categories ON items.category_id = categories.id
            WHERE 1";
<<<<<<< HEAD
if ($filter_cat != "") {
    $inv_sql .= " AND category_id = '$filter_cat'";
}
$inventory = $conn->query($inv_sql);

// STOCK LOG QUERY
=======

if ($filter_cat != "") {
    $inv_sql .= " AND category_id = '$filter_cat'";
}

$inventory = $conn->query($inv_sql);

// ======================
// STOCK LOG QUERY
// ======================
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
$log_sql = "SELECT sl.*, i.name AS item_name, u.fullname
            FROM stock_logs sl
            JOIN items i ON sl.item_id = i.id
            JOIN users u ON sl.user_id = u.id
            WHERE 1";
<<<<<<< HEAD
if ($from_date != "") $log_sql .= " AND DATE(sl.date) >= '$from_date'";
if ($to_date   != "") $log_sql .= " AND DATE(sl.date) <= '$to_date'";
$logs = $conn->query($log_sql);

// FETCH CATEGORIES
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<style>
    .report-title { color: #2a1a4f; font-weight: 700; }
    .card { border-radius: 8px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .table thead th { background-color: #343a40; color: white; border: none; padding: 12px; }
    
    @media print {
        .topbar, .offcanvas, .btn, form, .global-footer, .mb-4, .text-muted {
            display: none !important;
        }
        body, .container-fluid { background: white !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: none !important; }
        .table-responsive { overflow: visible !important; }
        table { width: 100% !important; border: 1px solid #dee2e6 !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 30px; }
    }
</style>

<div class="container-fluid px-0">
    <div class="print-header d-none">
        <h3 class="fw-bold">Supply Inventory Management System</h3>
        <h5>Inventory and Stock Transaction Report</h5>
        <p class="small">Generated on: <?= date('M d, Y | h:i A') ?></p>
        <hr>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="report-title mb-0">System Reports</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="page" value="reports">
                
                <div class="col-md-3">
                    <label class="small fw-bold mb-1">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <?php $categories->data_seek(0); while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filter_cat==$c['id'])?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="small fw-bold mb-1">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="<?= $from_date ?>">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold mb-1">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="<?= $to_date ?>">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-secondary btn-sm w-100">Filter</button>
                </div>

                <div class="col-md-4 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <a class="btn btn-success btn-sm" href="admin/export_inventory.php?category=<?= $filter_cat ?>">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Print PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-5">
        <h6 class="fw-bold mb-3 text-muted"><i class="bi bi-table me-2"></i>Inventory Summary</h6>
        <div class="card shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center small">
                            <th class="ps-3 text-start">Item Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Unit</th>
                            <th>Supplier</th>
                            <th class="pe-3 text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($inventory->num_rows > 0): ?>
                            <?php while ($row = $inventory->fetch_assoc()): ?>
                                <tr class="small">
                                    <td class="ps-3 fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['cat_name']) ?></td>
                                    <td class="text-center"><?= $row['quantity'] ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['unit']) ?></td>
                                    <td class="text-center small text-muted"><?= htmlspecialchars($row['supplier']) ?></td>
                                    <td class="pe-3 text-end fw-semibold text-primary"><?= number_format($row['price'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted small">No items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <h6 class="fw-bold mb-3 text-muted"><i class="bi bi-clock-history me-2"></i>Stock History Logs</h6>
        <div class="card shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center small">
                            <th class="ps-3 text-start">Timestamp</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Action Type</th>
                            <th class="pe-3 text-end">Handled By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($logs->num_rows > 0): ?>
                            <?php while ($row = $logs->fetch_assoc()): ?>
                                <tr class="small">
                                    <td class="ps-3 text-muted small"><?= date('M d, Y | h:i A', strtotime($row['date'])) ?></td>
                                    <td class="text-center fw-semibold"><?= htmlspecialchars($row['item_name']) ?></td>
                                    <td class="text-center"><?= $row['quantity'] ?></td>
                                    <td class="text-center">
                                        <?php if(in_array($row['type'], ['In', 'Addition'])): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">Stock In</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1">Stock Out</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-3 text-end small text-muted"><?= htmlspecialchars($row['fullname']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted small">No history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
=======

if ($from_date != "") $log_sql .= " AND DATE(sl.date) >= '$from_date'";
if ($to_date   != "") $log_sql .= " AND DATE(sl.date) <= '$to_date'";

$logs = $conn->query($log_sql);

// ======================
// EXPORT EXCEL (CSV)
// ======================
if ($export == "excel") {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=inventory_report.csv");

    $out = fopen("php://output", "w");
    fputcsv($out, ["Item", "Category", "Quantity", "Unit", "Supplier", "Price"]);

    $invExport = $conn->query($inv_sql);
    while ($r = $invExport->fetch_assoc()) {
        fputcsv($out, [
            $r['name'],
            $r['cat_name'],
            $r['quantity'],
            $r['unit'],
            $r['supplier'],
            $r['price']
        ]);
    }
    fclose($out);
    exit;
}

// ======================
// EXPORT PDF (PRINT)
// ======================
if ($export == "pdf") {
    echo "<h3>Inventory Report</h3>";
    echo "<table border='1' width='100%' cellpadding='5'>
            <tr>
                <th>Item</th><th>Category</th><th>Qty</th>
                <th>Unit</th><th>Supplier</th><th>Price</th>
            </tr>";

    $invExport = $conn->query($inv_sql);
    while ($r = $invExport->fetch_assoc()) {
        echo "<tr>
                <td>{$r['name']}</td>
                <td>{$r['cat_name']}</td>
                <td>{$r['quantity']}</td>
                <td>{$r['unit']}</td>
                <td>{$r['supplier']}</td>
                <td>{$r['price']}</td>
              </tr>";
    }
    echo "</table>";

    echo "<script>window.print();</script>";
    exit;
}

// FETCH CATEGORIES
$categories = $conn->query("SELECT * FROM categories");
?>

<h4 class="mb-3">Reports</h4>

<!-- FILTER -->
<form method="GET" class="row g-2 mb-4">
    <input type="hidden" name="page" value="reports">

    <div class="col-md-3">
        <select name="category" class="form-control">
            <option value="">All Categories</option>
            <?php while ($c = $categories->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= ($filter_cat==$c['id'])?'selected':'' ?>>
                    <?= $c['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-2">
        <input type="date" name="from" class="form-control" value="<?= $from_date ?>">
    </div>

    <div class="col-md-2">
        <input type="date" name="to" class="form-control" value="<?= $to_date ?>">
    </div>

    <div class="col-md-2">
        <button class="btn btn-secondary w-100">Filter</button>
    </div>

    <div class="col-md-3 d-flex gap-2">
        <a class="btn btn-success w-100"
           href="dashboard.php?page=reports&export=excel&category=<?= $filter_cat ?>&from=<?= $from_date ?>&to=<?= $to_date ?>">
           Export Excel
        </a>

        <a class="btn btn-danger w-100"
           href="dashboard.php?page=reports&export=pdf&category=<?= $filter_cat ?>&from=<?= $from_date ?>&to=<?= $to_date ?>">
           Export PDF
        </a>
    </div>
</form>

<!-- INVENTORY REPORT -->
<h5>Inventory Report</h5>
<table class="table table-bordered table-sm mb-4">
    <thead class="table-dark">
        <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Supplier</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $inventory->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['cat_name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['unit'] ?></td>
            <td><?= $row['supplier'] ?></td>
            <td><?= $row['price'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<!-- STOCK LOG REPORT -->
<h5>Stock Logs</h5>
<table class="table table-bordered table-sm">
    <thead class="table-dark">
        <tr>
            <th>Date</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Type</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $logs->fetch_assoc()): ?>
        <tr>
            <td><?= $row['date'] ?></td>
            <td><?= $row['item_name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['type'] ?></td>
            <td><?= $row['fullname'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
