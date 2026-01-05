<?php
session_start();
include '../database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit;
}

$q = $_GET['q'] ?? '';
$like = "%$q%";

$sql = "SELECT sl.*, i.name AS item_name, u.fullname 
        FROM stock_logs sl
        JOIN items i ON sl.item_id = i.id
        JOIN users u ON sl.user_id = u.id
        WHERE i.name LIKE ?
        ORDER BY sl.date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <tr>
        <td class="ps-3 small text-muted"><?= date('M d, Y | h:i A', strtotime($row['date'])) ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($row['item_name']) ?></td>
        <td class="text-center"><?= $row['quantity'] ?></td>
        <td class="text-center">
            <?php if(in_array($row['type'], ['In', 'Addition'])): ?>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 w-75 py-2">Stock In</span>
            <?php else: ?>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 w-75 py-2">Stock Out</span>
            <?php endif; ?>
        </td>
        <td class="text-muted small"><i class="bi bi-person me-1"></i><?= htmlspecialchars($row['fullname']) ?></td>
    </tr>
<?php 
    endwhile; 
else: 
    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No transaction history found for '$q'.</td></tr>";
endif;
?>