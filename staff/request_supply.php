<?php
// SECURITY: Staff only check
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger m-3'>Access denied.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// FETCH ITEMS WITH STOCK
$items = $conn->query("
    SELECT id, name, quantity 
    FROM items 
    WHERE quantity > 0
    ORDER BY name ASC
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
        // FIXED: JavaScript Redirect to prevent header warnings
        echo "<script>window.location.href='dashboard.php?page=request_supply&status=error';</script>";
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO requests (item_id, user_id, quantity, admin_comment)
        VALUES (?,?,?,?)
    ");
    // Using admin_comment field to store the staff's initial reason/purpose
    $stmt->bind_param("iiis", $item_id, $user_id, $qty, $reason);
    $stmt->execute();

    echo "<script>window.location.href='dashboard.php?page=request_supply&status=success';</script>";
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Request Supplies</h4>
            <p class="text-muted small mb-0">Fill out the form below to request office materials.</p>
        </div>
        <div class="bg-primary bg-opacity-10 p-2 rounded-3">
            <i class="bi bi-pencil-square text-primary fs-4"></i>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4 p-md-5">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Select Material</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-box-seam"></i></span>
                                    <select name="item_id" class="form-select border-start-0 py-2" required>
                                        <option value="">-- Choose an item --</option>
                                        <?php while ($i = $items->fetch_assoc()): ?>
                                            <option value="<?= $i['id'] ?>">
                                                <?= htmlspecialchars($i['name']) ?> (Stock: <?= $i['quantity'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Quantity</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-hash"></i></span>
                                    <input type="number" name="quantity" class="form-control border-start-0 py-2" min="1" placeholder="0" required>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Purpose of Request</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-chat-left-text"></i></span>
                                    <input type="text" name="reason" class="form-control border-start-0 py-2" placeholder="e.g. For Monthly Inventory" required>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button name="request" type="submit" class="btn btn-primary w-100 py-2 shadow-sm rounded-3">
                                    <i class="bi bi-send me-2"></i> Submit Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 bg-primary text-white shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Reminders:</h6>
                    <ul class="small opacity-75 mb-0">
                        <li class="mb-2">Ensure your requested quantity does not exceed the available stock.</li>
                        <li class="mb-2">Admin approval is required before supplies can be released.</li>
                        <li>You can track your request status in the <strong>"My Requests"</strong> section.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['status'])): ?>
<script>
    const status = "<?= $_GET['status'] ?>";
    if (status === 'success') {
        Swal.fire({ 
            icon: 'success', 
            title: 'Success!', 
            text: 'Supply request sent for admin review.', 
            confirmButtonColor: '#3e1f77'
        });
    } else if (status === 'error') {
        Swal.fire({ 
            icon: 'error', 
            title: 'Inventory Error', 
            text: 'Requested quantity exceeds available stock.',
            confirmButtonColor: '#d33'
        });
    }
</script>
<?php endif; ?>