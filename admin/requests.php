<?php
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

$admin_id = $_SESSION['user_id'];

// APPROVE REQUEST
if (isset($_POST['approve'])) {
    $req_id = $_POST['req_id'];
    $item_id = $_POST['item_id'];
    $qty = $_POST['quantity'];

    // Check stock
    $check = $conn->query("SELECT quantity FROM items WHERE id=$item_id");
    $stock = $check->fetch_assoc()['quantity'];

    if ($stock < $qty) {
        echo "<div class='alert alert-danger'>Insufficient stock.</div>";
    } else {
        // Deduct stock
        $conn->query("UPDATE items SET quantity = quantity - $qty WHERE id=$item_id");

        // Log stock out
        $conn->query("
            INSERT INTO stock_logs (item_id, type, quantity, user_id)
            VALUES ($item_id, 'Out', $qty, $admin_id)
        ");

        // Update request
        $conn->query("
            UPDATE requests 
            SET status='Approved', admin_comment='Approved'
            WHERE id=$req_id
        ");

        echo "<div class='alert alert-success'>Request approved.</div>";
    }
}

// DENY REQUEST
if (isset($_POST['deny'])) {
    $req_id = $_POST['req_id'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("
        UPDATE requests 
        SET status='Denied', admin_comment=? 
        WHERE id=?
    ");
    $stmt->bind_param("si", $comment, $req_id);
    $stmt->execute();

    echo "<div class='alert alert-warning'>Request denied.</div>";
}

// FETCH REQUESTS
$result = $conn->query("
    SELECT r.*, i.name AS item_name, u.fullname 
    FROM requests r
    JOIN items i ON r.item_id = i.id
    JOIN users u ON r.user_id = u.id
    ORDER BY r.request_date DESC
");
?>

<h4>Requests & Approval</h4>

<table class="table table-bordered table-sm">
    <tr class="table-dark">
        <th>Date</th>
        <th>Staff</th>
        <th>Item</th>
        <th>Qty</th>
        <th>Status</th>
        <th>Reason / Comment</th>
        <th width="260">Action</th>
    </tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<form method="POST">
    <td><?= $row['request_date'] ?></td>
    <td><?= $row['fullname'] ?></td>
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
    <td>
        <input type="text" name="comment" class="form-control"
               value="<?= $row['admin_comment'] ?>">
    </td>
    <td>
        <?php if ($row['status'] == 'Pending'): ?>
            <input type="hidden" name="req_id" value="<?= $row['id'] ?>">
            <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
            <input type="hidden" name="quantity" value="<?= $row['quantity'] ?>">

            <button name="approve" class="btn btn-success btn-sm">Approve</button>
            <button name="deny" class="btn btn-danger btn-sm">Deny</button>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</form>
</tr>
<?php endwhile; ?>
</table>
