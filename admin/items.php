<?php
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

// FETCH CATEGORIES
$categories = $conn->query("SELECT * FROM categories");

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

    header("Location: dashboard.php?page=items&status=added");
    exit;
}

/* ======================
   DELETE ITEM
====================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM items WHERE id=$id");

    header("Location: dashboard.php?page=items&status=deleted");
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

    header("Location: dashboard.php?page=items&status=updated");
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

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h4 class="mb-3">Item Management</h4>

<!-- ADD ITEM -->
<form method="POST" class="row g-2 mb-4">
    <div class="col-md-2">
        <input name="name" class="form-control" placeholder="Item Name" required>
    </div>
    <div class="col-md-2">
        <select name="category" class="form-control" required>
            <option value="">Category</option>
            <?php while ($c = $categories->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-1">
        <input name="quantity" type="number" class="form-control" placeholder="Qty" required>
    </div>
    <div class="col-md-1">
        <input name="unit" class="form-control" placeholder="Unit">
    </div>
    <div class="col-md-2">
        <input name="supplier" class="form-control" placeholder="Supplier">
    </div>
    <div class="col-md-2">
        <input name="price" type="number" step="0.01" class="form-control" placeholder="Price">
    </div>
    <div class="col-md-2">
        <button name="add" class="btn btn-primary w-100">Add Item</button>
    </div>
</form>

<!-- ITEMS TABLE -->
<div class="card">
<div class="card-body p-0">
<table class="table table-bordered table-hover mb-0">
    <thead class="table-dark">
        <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Unit</th>
            <th>Supplier</th>
            <th>Price</th>
            <th width="200">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <form method="POST">
        <tr>
            <td>
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input name="name" value="<?= $row['name'] ?>" class="form-control">
            </td>
            <td>
                <select name="category" class="form-control">
                    <?php
                    $catLoop = $conn->query("SELECT * FROM categories");
                    while ($c = $catLoop->fetch_assoc()):
                    ?>
                        <option value="<?= $c['id'] ?>" <?= ($row['category_id']==$c['id'])?'selected':'' ?>>
                            <?= $c['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
            <td><input name="quantity" type="number" value="<?= $row['quantity'] ?>" class="form-control"></td>
            <td><input name="unit" value="<?= $row['unit'] ?>" class="form-control"></td>
            <td><input name="supplier" value="<?= $row['supplier'] ?>" class="form-control"></td>
            <td><input name="price" type="number" step="0.01" value="<?= $row['price'] ?>" class="form-control"></td>
            <td class="text-center">
                <button name="update" class="btn btn-success btn-sm">Update</button>
                <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="confirmDelete(<?= $row['id'] ?>)">
                        Delete
                </button>
            </td>
        </tr>
        </form>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
</div>

<!-- SweetAlert Delete -->
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This item will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "dashboard.php?page=items&delete=" + id;
        }
    });
}
</script>

<!-- SweetAlert Status -->
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
