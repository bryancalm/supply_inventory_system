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

while ($row = $result->fetch_assoc()):
?>
<tr>
    <form method="POST" action="dashboard.php?page=categories">
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
            <button name="update" class="btn btn-success btn-sm">Update</button>
            <a href="dashboard.php?page=categories&delete=<?= $row['id'] ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this category?')">
               Delete
            </a>
        </td>
    </form>
</tr>
<?php endwhile; ?>
