<?php
<<<<<<< HEAD
// SECURITY: Admin only check
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger m-3'>Access denied.</div>";
    exit;
}

=======
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// FILTER STOCK LOGS
$search_item = $_GET['search'] ?? "";
$sql_logs = "
    SELECT sl.*, i.name AS item_name, u.fullname 
    FROM stock_logs sl
    JOIN items i ON sl.item_id = i.id
    JOIN users u ON sl.user_id = u.id
    WHERE i.name LIKE '%$search_item%'
    ORDER BY sl.date DESC
";
$logs = $conn->query($sql_logs);

>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
// FETCH CURRENT STOCK
$items = $conn->query("SELECT items.*, categories.name AS cat_name 
                        FROM items 
                        JOIN categories ON items.category_id = categories.id
                        ORDER BY items.name ASC");
<<<<<<< HEAD

// INITIAL FETCH FOR STOCK HISTORY (Last 20 records)
$sql_logs = "
    SELECT sl.*, i.name AS item_name, u.fullname 
    FROM stock_logs sl
    JOIN items i ON sl.item_id = i.id
    JOIN users u ON sl.user_id = u.id
    ORDER BY sl.date DESC LIMIT 20
";
$logs = $conn->query($sql_logs);
?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Stock Monitoring</h4>
        <div class="text-muted small">Real-time inventory & logs</div>
    </div>

    <div class="card border-0 shadow-sm mb-5" style="border-radius: 12px;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>Current Stock Level</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr class="small">
                            <th class="ps-4">Item</th>
                            <th>Category</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit</th>
                            <th>Supplier</th>
                            <th class="text-end pe-4">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3">
                                    <?= htmlspecialchars($row['cat_name']) ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold <?= ($row['quantity'] <= 5) ? 'text-danger' : '' ?>">
                                <?= $row['quantity'] ?>
                            </td>
                            <td class="text-center text-muted small"><?= htmlspecialchars($row['unit']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($row['supplier']) ?></td>
                            <td class="text-end pe-4 fw-semibold text-primary"><?= number_format($row['price'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0 fw-bold text-warning"><i class="bi bi-clock-history me-2"></i>Stock History Logs</h6>
        </div>
        <div class="card-body">
            <div class="input-group mb-4 shadow-sm border rounded-3 overflow-hidden">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchStock" class="form-control border-0 py-2" placeholder="Search history by item name...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-3 border-0">Date & Time</th>
                            <th class="border-0">Item Name</th>
                            <th class="text-center border-0">Quantity</th>
                            <th class="text-center border-0">Transaction Type</th>
                            <th class="pe-3 border-0">Processed By</th>
                        </tr>
                    </thead>
                    <tbody id="stockHistoryTable">
                        <?php if($logs->num_rows > 0): ?>
                            <?php while($row = $logs->fetch_assoc()): ?>
                            <tr class="border-bottom border-light">
                                <td class="ps-3 small text-muted"><?= date('M d, Y | h:i A', strtotime($row['date'])) ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($row['item_name']) ?></td>
                                <td class="text-center fw-bold"><?= $row['quantity'] ?></td>
                                <td class="text-center">
                                    <?php if($row['type'] == 'In' || $row['type'] == 'Addition'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 w-75 py-2">Stock In</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 w-75 py-2">Stock Out</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small"><i class="bi bi-person me-1"></i><?= htmlspecialchars($row['fullname']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No stock logs found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const searchStock = document.getElementById("searchStock");
const stockHistoryTable = document.getElementById("stockHistoryTable");

searchStock.addEventListener("keyup", function () {
    // This calls the file created in Step 1
    fetch("admin/stock_search.php?q=" + encodeURIComponent(this.value))
        .then(res => res.text())
        .then(data => {
            stockHistoryTable.innerHTML = data;
        });
});
</script>
=======
?>

<h4>Stock Monitoring</h4>

<!-- CURRENT STOCK -->
<h5>Current Stock</h5>
<table class="table table-bordered table-sm mb-4">
    <tr class="table-dark">
        <th>Item</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Supplier</th>
        <th>Price</th>
    </tr>
<?php while($row = $items->fetch_assoc()): ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['cat_name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= $row['unit'] ?></td>
    <td><?= $row['supplier'] ?></td>
    <td><?= $row['price'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<!-- STOCK LOGS -->
<h5>Stock History</h5>

<form method="GET" class="mb-2">
    <input type="hidden" name="page" value="stock">
    <input type="text" name="search" class="form-control" placeholder="Search item..." value="<?= $search_item ?>">
</form>

<table class="table table-bordered table-sm">
    <tr class="table-dark">
        <th>Date</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Type</th>
        <th>User</th>
    </tr>
<?php while($row = $logs->fetch_assoc()): ?>
<tr>
    <td><?= $row['date'] ?></td>
    <td><?= $row['item_name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= $row['type'] ?></td>
    <td><?= $row['fullname'] ?></td>
</tr>
<?php endwhile; ?>
</table>
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
