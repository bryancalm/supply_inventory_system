<?php
// SECURITY: Admin only check
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger m-3'>Access denied.</div>";
    exit;
}

$admin_id = $_SESSION['user_id'];

// APPROVE REQUEST
if (isset($_POST['approve'])) {
    $req_id = $_POST['req_id'];
    $item_id = $_POST['item_id'];
    $qty = $_POST['quantity'];

    // Check stock level before approving
    $check = $conn->query("SELECT quantity FROM items WHERE id=$item_id");
    $stock = $check->fetch_assoc()['quantity'];

    if ($stock < $qty) {
        echo "<script>window.location.href='dashboard.php?page=requests&status=low_stock';</script>";
    } else {
        // Deduct stock and log the transaction
        $conn->query("UPDATE items SET quantity = quantity - $qty WHERE id=$item_id");

        $conn->query("
            INSERT INTO stock_logs (item_id, type, quantity, user_id)
            VALUES ($item_id, 'Out', $qty, $admin_id)
        ");

        // Update request status
        $conn->query("
            UPDATE requests 
            SET status='Approved', admin_comment='Approved'
            WHERE id=$req_id
        ");

        echo "<script>window.location.href='dashboard.php?page=requests&status=approved';</script>";
    }
    exit;
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

    echo "<script>window.location.href='dashboard.php?page=requests&status=denied';</script>";
    exit;
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Requests & Approval</h4>
        <div class="text-muted small">Manage staff supply requests</div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr class="text-center small">
                            <th class="ps-3 text-start" style="min-width: 160px;">Date Request</th>
                            <th style="min-width: 140px;">Staff Name</th>
                            <th style="min-width: 150px;">Item Requested</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th style="min-width: 200px;">Reason / Comment</th>
                            <th class="pe-3" style="min-width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td class="ps-3 small text-muted">
                                    <?= date('M d, Y | h:i A', strtotime($row['request_date'])) ?>
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['fullname']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td class="text-center fw-bold"><?= $row['quantity'] ?></td>
                                <td class="text-center">
                                    <?php 
                                        $statusClass = 'bg-warning text-warning';
                                        if($row['status'] == 'Approved') $statusClass = 'bg-success text-success';
                                        if($row['status'] == 'Denied') $statusClass = 'bg-danger text-danger';
                                    ?>
                                    <span class="badge <?= $statusClass ?> bg-opacity-10 border border-current border-opacity-25 px-3 py-1 w-100">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <input type="text" name="comment" class="form-control form-control-sm" placeholder="Add a comment...">
                                    <?php else: ?>
                                        <span class="small text-muted italic"><?= htmlspecialchars($row['admin_comment'] ?? '---') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-3 text-center">
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <input type="hidden" name="req_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                                        <input type="hidden" name="quantity" value="<?= $row['quantity'] ?>">

                                        <div class="d-flex gap-1 justify-content-center">
                                            <button name="approve" class="btn btn-success btn-sm flex-fill">Approve</button>
                                            <button name="deny" class="btn btn-danger btn-sm flex-fill">Deny</button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">---</span>
                                    <?php endif; ?>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No pending requests found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['status'])): ?>
<script>
    const status = "<?= $_GET['status'] ?>";
    if (status === 'approved') {
        Swal.fire({ icon:'success', title:'Approved', text:'Stock deducted and request updated.', timer:2000, showConfirmButton:false });
    } else if (status === 'denied') {
        Swal.fire({ icon:'info', title:'Request Denied', text:'The staff has been notified of the denial.', timer:2000, showConfirmButton:false });
    } else if (status === 'low_stock') {
        Swal.fire({ icon:'error', title:'Insufficient Stock', text:'Cannot approve request. Available stock is lower than requested quantity.' });
    }
</script>
<?php endif; ?>