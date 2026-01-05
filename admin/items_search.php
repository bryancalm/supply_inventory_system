<?php
session_start();
include '../database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit;
}

$q = $_GET['q'] ?? '';
$like = "%$q%";

$stmt = $conn->prepare(
    "SELECT items.*, categories.name AS cat_name
     FROM items
     JOIN categories ON items.category_id = categories.id
     WHERE items.name LIKE ?
     ORDER BY items.name ASC"
);
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
<tr>
    <form method="POST">
        <td>
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control form-control-sm">
        </td>
        <td>
            <select name="category" class="form-select form-select-sm">
                <?php
                $cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                while ($c = $cats->fetch_assoc()):
                ?>
                    <option value="<?= $c['id'] ?>" <?= ($row['category_id']==$c['id'])?'selected':'' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </td>
        <td>
            <input name="quantity" type="number" value="<?= $row['quantity'] ?>" class="form-control form-control-sm">
        </td>
        <td>
            <input name="unit" value="<?= htmlspecialchars($row['unit']) ?>" class="form-control form-control-sm">
        </td>
        <td>
            <input name="supplier" value="<?= htmlspecialchars($row['supplier']) ?>" class="form-control form-control-sm">
        </td>
        <td>
            <input name="price" type="number" step="0.01" value="<?= $row['price'] ?>" class="form-control form-control-sm">
        </td>
        <td>
            <div class="d-flex gap-1 justify-content-center">
                <button name="update" class="btn btn-success btn-sm flex-fill">Update</button>
                <button type="button" 
                        class="btn btn-danger btn-sm flex-fill" 
                        onclick="confirmDelete(<?= $row['id'] ?>)">
                    Delete
                </button>
            </div>
        </td>
    </form>
</tr>
<?php 
    endwhile; 
else: 
?>
    <tr>
        <td colspan="7" class="text-center py-4 text-muted">
            <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
            No items found matching "<strong><?= htmlspecialchars($q) ?></strong>"
        </td>
    </tr>
<?php endif; ?>