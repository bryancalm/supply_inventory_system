<?php
// SECURITY: Admin only
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

/* ======================
   ADD CATEGORY
====================== */
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare(
        "INSERT INTO categories (name, description) VALUES (?,?)"
    );
    $stmt->bind_param("ss", $name, $desc);
    $stmt->execute();

    // FIXED: Use JavaScript redirect to avoid Header Warning
    echo "<script>window.location.href='dashboard.php?page=categories&status=added';</script>";
    exit;
}

/* ======================
   DELETE CATEGORY
====================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // FIXED: Use JavaScript redirect
    echo "<script>window.location.href='dashboard.php?page=categories&status=deleted';</script>";
    exit;
}

/* ======================
   UPDATE CATEGORY
====================== */
if (isset($_POST['update'])) {
    $id   = $_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare(
        "UPDATE categories SET name=?, description=? WHERE id=?"
    );
    $stmt->bind_param("ssi", $name, $desc, $id);
    $stmt->execute();

    // FIXED: Use JavaScript redirect
    echo "<script>window.location.href='dashboard.php?page=categories&status=updated';</script>";
    exit;
}

/* ======================
   FETCH CATEGORIES
====================== */
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid mt-4">

<h4 class="fw-bold mb-3">Category Management</h4>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h6 class="mb-3 fw-bold">Add New Category</h6>
        <form method="POST" class="row g-3"> <div class="col-12 col-md-4">
                <label class="small fw-bold">Category Name</label>
                <input type="text" name="name" class="form-control" placeholder="Category Name" required>
            </div>
            <div class="col-12 col-md-5">
                <label class="small fw-bold">Description</label>
                <input type="text" name="description" class="form-control" placeholder="Description">
            </div>
            <div class="col-12 col-md-3 d-flex align-items-end">
                <button name="add" class="btn btn-primary w-100">Add Category</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3 shadow-sm">
    <div class="card-body">
        <input type="text" id="searchCategory" class="form-control" placeholder="Search category...">
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th style="min-width: 200px;">Name</th>
                        <th>Description</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTable">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="text" name="name"
                                           value="<?= htmlspecialchars($row['name']) ?>"
                                           class="form-control form-control-sm" required>
                                </td>
                                <td>
                                    <input type="text" name="description"
                                           value="<?= htmlspecialchars($row['description']) ?>"
                                           class="form-control form-control-sm">
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button name="update" class="btn btn-success btn-sm flex-fill">
                                            Update
                                        </button>
                                        <button type="button"
                                                class="btn btn-danger btn-sm flex-fill"
                                                onclick="confirmDelete(<?= $row['id'] ?>)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No categories found in the system.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This category will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "dashboard.php?page=categories&delete=" + id;
        }
    });
}
</script>

<?php if (isset($_GET['status'])): ?>
<script>
<?php if ($_GET['status'] == 'added'): ?>
Swal.fire({ icon: 'success', title: 'Category Added', timer: 1500, showConfirmButton: false });
<?php elseif ($_GET['status'] == 'updated'): ?>
Swal.fire({ icon: 'success', title: 'Category Updated', timer: 1500, showConfirmButton: false });
<?php elseif ($_GET['status'] == 'deleted'): ?>
Swal.fire({ icon: 'success', title: 'Category Deleted', timer: 1500, showConfirmButton: false });
<?php endif; ?>
</script>
<?php endif; ?>

<script>
const searchCategory = document.getElementById("searchCategory");
const categoryTable = document.getElementById("categoryTable");

searchCategory.addEventListener("keyup", function () {
    fetch("admin/categories_search.php?q=" + encodeURIComponent(this.value))
        .then(res => res.text())
        .then(data => categoryTable.innerHTML = data);
});
</script>