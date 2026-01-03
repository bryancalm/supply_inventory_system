<?php
session_start();
include '../database.php';

// SECURITY
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Staff') {
    exit;
}

$q = $_GET['q'] ?? "";

$stmt = $conn->prepare("
    SELECT items.*, categories.name AS cat_name
    FROM items
    JOIN categories ON items.category_id = categories.id
    WHERE items.name LIKE ?
    ORDER BY items.name ASC
");

$like = "%$q%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['cat_name']) ?></td>
    <td><?= $row['quantity'] ?></td>
    <td><?= htmlspecialchars($row['unit']) ?></td>
    <td><?= htmlspecialchars($row['supplier']) ?></td>
    <td><?= $row['price'] ?></td>
</tr>
<?php endwhile; ?>
