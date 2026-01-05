<?php
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// FETCH CATEGORIES for the dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

/* ======================
   ADD ITEM
====================== */
if (isset($_POST['add'])) {
    $name     = $_POST['name'];
    $category = $_POST['category'];
    $qty      = $_POST['quantity'];
    $unit     = $_POST['unit'];
    $supplier = $_POST['supplier'];
    $price    = $_POST['price'];

    $stmt = $conn->prepare(
        "INSERT INTO items (name, category_id, quantity, unit, supplier, price)
         VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param("siissd", $name, $category, $qty, $unit, $supplier, $price);
    $stmt->execute();

    echo "<script>window.location.href='dashboard.php?page=items&status=added';</script>";
    exit;
}

/* ======================
   DELETE ITEM
====================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM items WHERE id=$id");

    echo "<script>window.location.href='dashboard.php?page=items&status=deleted';</script>";
    exit;
}

/* ======================
   UPDATE ITEM
====================== */
if (isset($_POST['update'])) {
    $id       = $_POST['id'];
    $name     = $_POST['name'];
    $category = $_POST['category'];
    $qty      = $_POST['quantity'];
    $unit     = $_POST['unit'];
    $supplier = $_POST['supplier'];
    $price    = $_POST['price'];

    $stmt = $conn->prepare(
        "UPDATE items
         SET name=?, category_id=?, quantity=?, unit=?, supplier=?, price=?
         WHERE id=?"
    );
    $stmt->bind_param("siissdi", $name, $category, $qty, $unit, $supplier, $price, $id);
    $stmt->execute();

    echo "<script>window.location.href='dashboard.php?page=items&status=updated';</script>";
    exit;
}

/* ======================
   FETCH ITEMS
====================== */
$result = $conn->query(
    "SELECT items.*, categories.name AS cat_name
     FROM items
     JOIN categories ON items.category_id = categories.id
     ORDER BY items.name ASC"
);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h4 class="mb-3 fw-bold">Item Management</h4>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h6 class="mb-3 fw-bold">Add New Item</h6>
        <form method="POST" class="row g-3">
            <div class="col-12 col-md-4 col-lg-2">
                <input name="name" class="form-control" placeholder="Item Name" required>
            </div>
            <div class="col-12 col-md-4 col-lg-2">
                <select name="category" class="form-select" required>
                    <option value="">Category</option>
                    <?php 
                    $categories->data_seek(0);
                    while ($c = $categories->fetch_assoc()): 
                    ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-6 col-md-2 col-lg-1">
                <input name="quantity" type="number" class="form-control" placeholder="Qty" required>
            </div>
            <div class="col-6 col-md-2 col-lg-1">
                <input name="unit" class="form-control" placeholder="Unit">
            </div>
            <div class="col-12 col-md-4 col-lg-2">
                <input name="supplier" class="form-control" placeholder="Supplier">
            </div>
            <div class="col-12 col-md-4 col-lg-2">
                <input name="price" type="number" step="0.01" class="form-control" placeholder="Price">
            </div>
            <div class="col-12 col-lg-2">
                <button name="add" class="btn btn-primary w-100">Add Item</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <input type="text" id="searchItems" class="form-control" placeholder="Search items by name, category, or supplier...">
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="min-width: 150px;">Item</th>
                        <th style="min-width: 150px;">Category</th>
                        <th style="min-width: 100px;">Stock</th>
                        <th style="min-width: 80px;">Unit</th>
                        <th style="min-width: 150px;">Supplier</th>
                        <th style="min-width: 100px;">Price</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTable">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <select name="category" class="form-select form-select-sm">
                                        <?php
                                        $catLoop = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                                        while ($c = $catLoop->fetch_assoc()):
                                        ?>
                                            <option value="<?= $c['id'] ?>" <?= ($row['category_id']==$c['id'])?'selected':'' ?>>
                                                <?= htmlspecialchars($c['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td><input name="quantity" type="number" value="<?= $row['quantity'] ?>" class="form-control form-control-sm text-center"></td>
                                <td><input name="unit" value="<?= htmlspecialchars($row['unit']) ?>" class="form-control form-control-sm text-center"></td>
                                <td><input name="supplier" value="<?= htmlspecialchars($row['supplier']) ?>" class="form-control form-control-sm"></td>
                                <td><input name="price" type="number" step="0.01" value="<?= $row['price'] ?>" class="form-control form-control-sm text-end"></td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center px-1">
                                        <button name="update" class="btn btn-success btn-sm flex-fill">Update</button>
                                        <button type="button" class="btn btn-danger btn-sm flex-fill" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No items found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// LIVE SEARCH LOGIC
const searchItems = document.getElementById("searchItems");
const itemsTable = document.getElementById("itemsTable");

searchItems.addEventListener("keyup", function () {
    fetch("admin/items_search.php?q=" + encodeURIComponent(this.value))
        .then(res => res.text())
        .then(data => itemsTable.innerHTML = data);
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This item will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "dashboard.php?page=items&delete=" + id;
        }
    });
}
</script>

<?php if (isset($_GET['status'])): ?>
<script>
<?php if ($_GET['status'] == 'added'): ?>
Swal.fire({ icon:'success', title:'Item Added', timer:1500, showConfirmButton:false });
<?php elseif ($_GET['status'] == 'updated'): ?>
Swal.fire({ icon:'success', title:'Item Updated', timer:1500, showConfirmButton:false });
<?php elseif ($_GET['status'] == 'deleted'): ?>
Swal.fire({ icon:'success', title:'Item Deleted', timer:1500, showConfirmButton:false });
<?php endif; ?>
</script>
<?php endif; ?>