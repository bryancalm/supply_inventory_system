<?php
// STAFF ONLY
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// FETCH STOCK
$result = $conn->query("
    SELECT items.*, categories.name AS cat_name
    FROM items
    JOIN categories ON items.category_id = categories.id
    ORDER BY items.name ASC
");
?>

<div class="container-fluid mt-4">

    <h4 class="fw-bold mb-3">Available Stock</h4>

    <!-- SEARCH -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control form-control-lg"
               placeholder="🔍 Search item, category, supplier...">
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle mb-0 text-center">
            <thead class="table-dark">
                <tr>
                    <th style="width:20%">Item</th>
                    <th style="width:15%">Category</th>
                    <th style="width:10%">Quantity</th>
                    <th style="width:10%">Unit</th>
                    <th style="width:25%">Supplier</th>
                    <th style="width:20%">Price</th>
                </tr>
            </thead>
            <tbody id="stockTable">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <!-- ITEM -->
                    <td class="text-start ps-3 fw-semibold">
                        <?= htmlspecialchars($row['name']) ?>
                    </td>

                    <!-- CATEGORY -->
                    <td>
                        <span class="badge bg-secondary px-2 py-1">
                            <?= htmlspecialchars($row['cat_name']) ?>
                        </span>
                    </td>

                    <!-- QUANTITY -->
                    <td>
                        <?php if ($row['quantity'] <= 5): ?>
                            <span class="badge bg-danger px-2 py-1">
                                <?= $row['quantity'] ?> ⚠️
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success px-2 py-1">
                                <?= $row['quantity'] ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- UNIT -->
                    <td><?= htmlspecialchars($row['unit']) ?></td>

                    <!-- SUPPLIER -->
                    <td class="text-start ps-3"><?= htmlspecialchars($row['supplier']) ?></td>

                    <!-- PRICE -->
                    <td class="text-end pe-3">
                        <?php 
                            $price = $row['price'];
                            // Price badge colors
                            if ($price > 1000) {
                                $badgeClass = "bg-warning text-dark"; // high price
                            } elseif ($price < 100) {
                                $badgeClass = "bg-info text-white";   // low price
                            } else {
                                $badgeClass = "bg-light text-dark";  // normal price
                            }
                        ?>
                        <span class="badge <?= $badgeClass ?> px-2 py-1">
                            ₱<?= number_format($price, 2, '.', ',') ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- LIVE SEARCH -->
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
