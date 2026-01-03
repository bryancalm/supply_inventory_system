<?php
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// FILTER VALUES
$filter_cat = $_GET['category'] ?? "";
$from_date  = $_GET['from'] ?? "";
$to_date    = $_GET['to'] ?? "";
$export     = $_GET['export'] ?? "";

// ======================
// INVENTORY QUERY
// ======================
$inv_sql = "SELECT items.*, categories.name AS cat_name
            FROM items
            JOIN categories ON items.category_id = categories.id
            WHERE 1";

if ($filter_cat != "") {
    $inv_sql .= " AND category_id = '$filter_cat'";
}

$inventory = $conn->query($inv_sql);

// ======================
// STOCK LOG QUERY
// ======================
$log_sql = "SELECT sl.*, i.name AS item_name, u.fullname
            FROM stock_logs sl
            JOIN items i ON sl.item_id = i.id
            JOIN users u ON sl.user_id = u.id
            WHERE 1";

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
