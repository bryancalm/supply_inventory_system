<?php
<<<<<<< HEAD
// SECURITY: Staff only check
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger m-3'>Access denied.</div>";
=======
// STAFF ONLY
if ($_SESSION['role'] != 'Staff') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
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

<<<<<<< HEAD
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">My Requests History</h4>
            <p class="text-muted small mb-0">Track the status of your submitted supply requests.</p>
        </div>
        <div class="bg-warning bg-opacity-10 p-2 rounded-3">
            <i class="bi bi-card-checklist text-warning fs-4"></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-3">Date Requested</th>
                        <th>Item Name</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Status</th>
                        <th class="pe-4">Admin Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 small text-muted">
                                <?= date('M d, Y | h:i A', strtotime($row['request_date'])) ?>
                            </td>

                            <td class="fw-bold text-dark">
                                <?= htmlspecialchars($row['item_name']) ?>
                            </td>

                            <td class="text-center">
                                <span class="fw-semibold"><?= $row['quantity'] ?></span>
                            </td>

                            <td class="text-center">
                                <?php 
                                    $statusClass = 'bg-warning text-warning'; // Pending
                                    if($row['status'] == 'Approved') $statusClass = 'bg-success text-success';
                                    if($row['status'] == 'Denied') $statusClass = 'bg-danger text-danger';
                                ?>
                                <span class="badge <?= $statusClass ?> bg-opacity-10 border border-current border-opacity-25 px-3 py-1 w-75">
                                    <?= $row['status'] ?>
                                </span>
                            </td>

                            <td class="pe-4 small text-muted">
                                <em><?= htmlspecialchars($row['admin_comment'] ?? 'Waiting for review...') ?></em>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                You haven't made any requests yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
=======
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
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
