<?php
// STAFF ONLY
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// FETCH STAFF REQUESTS
$result = $conn->query("
    SELECT r.*, i.name AS item_name
    FROM requests r
    JOIN items i ON r.item_id = i.id
    WHERE r.user_id = $user_id
    ORDER BY r.request_date DESC
");
?>

<h4>My Requests</h4>

<table class="table table-bordered table-sm">
    <tr class="table-dark">
        <th>Date</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Admin Comment</th>
    </tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['request_date'] ?></td>
    <td><?= $row['item_name'] ?></td>
    <td><?= $row['quantity'] ?></td>
    <td>
        <span class="badge 
            <?= $row['status']=='Pending'?'bg-warning':'' ?>
            <?= $row['status']=='Approved'?'bg-success':'' ?>
            <?= $row['status']=='Denied'?'bg-danger':'' ?>">
            <?= $row['status'] ?>
        </span>
    </td>
    <td><?= $row['admin_comment'] ?></td>
</tr>
<?php endwhile; ?>
</table>
