<?php
// STAFF ONLY
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// FETCH ITEMS WITH STOCK
$items = $conn->query("
    SELECT id, name, quantity 
    FROM items 
    WHERE quantity > 0
");

// SUBMIT REQUEST
if (isset($_POST['request'])) {
    $item_id = $_POST['item_id'];
    $qty = $_POST['quantity'];
    $reason = $_POST['reason'];

    // Check available stock
    $check = $conn->query("SELECT quantity FROM items WHERE id=$item_id");
    $stock = $check->fetch_assoc()['quantity'];

    if ($qty > $stock) {
        echo "<div class='alert alert-danger'>Requested quantity exceeds available stock.</div>";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO requests (item_id, user_id, quantity, admin_comment)
            VALUES (?,?,?,?)
        ");
        $stmt->bind_param("iiis", $item_id, $user_id, $qty, $reason);
        $stmt->execute();

        echo "<div class='alert alert-success'>Request submitted successfully.</div>";
    }
}
?>

<h4>Request Supplies</h4>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Item</label>
        <select name="item_id" class="form-control" required>
            <option value="">Select Item</option>
            <?php while ($i = $items->fetch_assoc()): ?>
                <option value="<?= $i['id'] ?>">
                    <?= $i['name'] ?> (Available: <?= $i['quantity'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" min="1" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Reason</label>
        <input type="text" name="reason" class="form-control" placeholder="Purpose of request" required>
    </div>

    <div class="col-md-12">
        <button name="request" class="btn btn-primary">Submit Request</button>
    </div>
</form>
