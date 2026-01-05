<?php
<<<<<<< HEAD
// STAFF ONLY check
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger m-3'>Access denied.</div>";
    exit;
}

// FETCH STOCK
=======
// STAFF ONLY
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// DEFAULT FETCH
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
$result = $conn->query("
    SELECT items.*, categories.name AS cat_name
    FROM items
    JOIN categories ON items.category_id = categories.id
    ORDER BY items.name ASC
");
?>

<<<<<<< HEAD
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Available Stock</h4>
            <p class="text-muted small mb-0">View and search real-time inventory levels.</p>
        </div>
        <div class="bg-success bg-opacity-10 p-2 rounded-3">
            <i class="bi bi-box-seam text-success fs-4"></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="search" class="form-control border-start-0 ps-0 shadow-none" 
                       placeholder="Search by item name, category, or supplier...">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-3">Item Details</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Unit</th>
                        <th>Supplier</th>
                        <th class="pe-4 text-end">Unit Price</th>
                    </tr>
                </thead>
                <tbody id="stockTable">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold d-block text-dark"><?= htmlspecialchars($row['name']) ?></span>
                            </td>

                            <td class="text-center">
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3">
                                    <?= htmlspecialchars($row['cat_name']) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <?php if ($row['quantity'] <= 5): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1">
                                        <?= $row['quantity'] ?> <i class="bi bi-exclamation-triangle ms-1"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">
                                        <?= $row['quantity'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center text-muted small"><?= htmlspecialchars($row['unit']) ?></td>

                            <td class="text-muted small"><?= htmlspecialchars($row['supplier']) ?></td>

                            <td class="pe-4 text-end">
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50 px-3 py-1 fw-bold">
                                    ₱<?= number_format($row['price'], 2) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No stock items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

=======
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
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
<script>
const searchInput = document.getElementById("search");
const stockTable  = document.getElementById("stockTable");

searchInput.addEventListener("keyup", function () {
    const query = this.value;
<<<<<<< HEAD
    // FETCH via your existing staff/view_stock_search.php
=======

>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
    fetch("staff/view_stock_search.php?q=" + encodeURIComponent(query))
        .then(res => res.text())
        .then(data => {
            stockTable.innerHTML = data;
        });
});
<<<<<<< HEAD
</script>
=======
</script>
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
