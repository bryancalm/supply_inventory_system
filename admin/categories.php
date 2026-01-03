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

    header("Location: dashboard.php?page=categories");
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

    header("Location: dashboard.php?page=categories");
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

    header("Location: dashboard.php?page=categories");
    exit;
}

/* ======================
   FETCH CATEGORIES
====================== */
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<h4 class="mb-3">Category Management</h4>

<!-- ADD CATEGORY -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="mb-3">Add Category</h6>
        <form method="POST" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control"
                       placeholder="Category Name" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="description" class="form-control"
                       placeholder="Description">
            </div>
            <div class="col-md-3 text-end">
                <button name="add" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- LIVE SEARCH -->
<div class="card mb-3">
    <div class="card-body">
        <input type="text" id="searchCategory"
               class="form-control"
               placeholder="Search category...">
    </div>
</div>

<!-- CATEGORY TABLE -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th width="180">Action</th>
                </tr>
            </thead>
            <tbody id="categoryTable">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="POST">
                        <td>
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="text" name="name"
                                   value="<?= $row['name'] ?>"
                                   class="form-control" required>
                        </td>
                        <td>
                            <input type="text" name="description"
                                   value="<?= $row['description'] ?>"
                                   class="form-control">
                        </td>
                        <td class="text-center">
                            <button name="update"
                                    class="btn btn-success btn-sm">Update</button>
                            <a href="dashboard.php?page=categories&delete=<?= $row['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this category?')">
                               Delete
                            </a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- LIVE SEARCH SCRIPT -->
<script>
const searchCategory = document.getElementById("searchCategory");
const categoryTable = document.getElementById("categoryTable");

searchCategory.addEventListener("keyup", function () {
    const query = this.value;

    fetch("admin/categories_search.php?q=" + encodeURIComponent(query))
        .then(res => res.text())
        .then(data => {
            categoryTable.innerHTML = data;
        });
});
</script>
