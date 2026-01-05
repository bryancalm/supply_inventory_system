<?php
session_start();
include '../database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit;
}

$q = $_GET['q'] ?? '';
$like = "%$q%";

$stmt = $conn->prepare(
    "SELECT * FROM categories WHERE name LIKE ? ORDER BY name ASC"
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
            <input type="text" name="name"
                   value="<?= htmlspecialchars($row['name']) ?>"
                   class="form-control form-control-sm" required>
        </td>
        <td>
            <input type="text" name="description"
                   value="<?= htmlspecialchars($row['description']) ?>"
                   class="form-control form-control-sm">
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
        <td colspan="3" class="text-center py-4 text-muted">
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            No categories found matching "<strong><?= htmlspecialchars($q) ?></strong>"
        </td>
    </tr>
<?php endif; ?>