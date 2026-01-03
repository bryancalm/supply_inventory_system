<?php
// STAFF ONLY
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// DEFAULT FETCH
$result = $conn->query("
    SELECT items.*, categories.name AS cat_name
    FROM items
    JOIN categories ON items.category_id = categories.id
    ORDER BY items.name ASC
");
?>

<h4 class="mb-3">Available Stock</h4>

<!-- SEARCH -->
<div class="card mb-3">
    <div class="card-body">
        <input type="text" id="search" class="form-control" placeholder="Search item...">
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
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
            <tbody id="stockTable">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['cat_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= htmlspecialchars($row['unit']) ?></td>
                    <td><?= htmlspecialchars($row['supplier']) ?></td>
                    <td><?= $row['price'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- LIVE SEARCH SCRIPT -->
<script>
const searchInput = document.getElementById("search");
const stockTable  = document.getElementById("stockTable");

searchInput.addEventListener("keyup", function () {
    const query = this.value;

    fetch("staff/view_stock_search.php?q=" + encodeURIComponent(query))
        .then(res => res.text())
        .then(data => {
            stockTable.innerHTML = data;
        });
});
</script>
