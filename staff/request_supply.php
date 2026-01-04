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

/* ======================
   SUBMIT REQUEST
====================== */
if (isset($_POST['request'])) {
    $item_id = $_POST['item_id'];
    $qty     = $_POST['quantity'];
    $reason  = $_POST['reason'];

    // Check available stock
    $check = $conn->prepare("SELECT quantity FROM items WHERE id=?");
    $check->bind_param("i", $item_id);
    $check->execute();
    $stock = $check->get_result()->fetch_assoc()['quantity'];

    if ($qty > $stock) {
        header("Location: dashboard.php?page=request&status=error");
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO requests (item_id, user_id, quantity, admin_comment)
        VALUES (?,?,?,?)
    ");
    $stmt->bind_param("iiis", $item_id, $user_id, $qty, $reason);
    $stmt->execute();

    header("Location: dashboard.php?page=request&status=success");
    exit;
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h4>Request Supplies</h4>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Item</label>
        <select name="item_id" class="form-control" required>
            <option value="">Select Item</option>
            <?php while ($i = $items->fetch_assoc()): ?>
                <option value="<?= $i['id'] ?>">
                    <?= htmlspecialchars($i['name']) ?> (Available: <?= $i['quantity'] ?>)
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
        <input type="text" name="reason" class="form-control"
               placeholder="Purpose of request" required>
    </div>

    <div class="col-md-12">
        <button name="request" class="btn btn-primary">
            Submit Request
        </button>
    </div>
</form>

<!-- SweetAlert STATUS -->
<?php if (isset($_GET['status'])): ?>
<script>
<?php if ($_GET['status'] == 'success'): ?>
Swal.fire({
    icon: 'success',
    title: 'Request Submitted',
    text: 'Your supply request has been sent successfully',
    timer: 1800,
    showConfirmButton: false
});
<?php elseif ($_GET['status'] == 'error'): ?>
Swal.fire({
    icon: 'error',
    title: 'Invalid Quantity',
    text: 'Requested quantity exceeds available stock'
});
<?php endif; ?>
</script>
<?php endif; ?>
