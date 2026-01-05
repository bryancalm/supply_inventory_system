<?php
session_start();
include '../database.php';

// SECURITY: Ensure only logged-in staff can access this search
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

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()):
?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['cat_name']) ?></td>
        <td class="text-center"><?= $row['quantity'] ?></td>
        <td class="text-center"><?= htmlspecialchars($row['unit']) ?></td>
        <td><?= htmlspecialchars($row['supplier']) ?></td>
        <td class="text-end"><?= number_format($row['price'], 2) ?></td>
    </tr>
<?php 
    endwhile; 
} else {
    // Show a clean message if no matches are found
    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>
            <i class='bi bi-search fs-2 d-block mb-2'></i>
            No items found matching '" . htmlspecialchars($q) . "'
          </td></tr>";
}
?>