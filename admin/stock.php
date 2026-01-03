<?php
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

// FETCH CURRENT STOCK
$items = $conn->query("SELECT items.*, categories.name AS cat_name 
                        FROM items 
                        JOIN categories ON items.category_id = categories.id
                        ORDER BY items.name ASC");
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
