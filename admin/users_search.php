<?php
session_start();
include '../database.php';

// ADMIN ONLY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit;
}

$q = $_GET['q'] ?? '';
$like = "%$q%";

$stmt = $conn->prepare(
    "SELECT * FROM users 
     WHERE fullname LIKE ? OR email LIKE ? OR username LIKE ? 
     ORDER BY fullname ASC"
);
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

// Check if any results were found
if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <tr>
        <form method="POST">
            <td>
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input name="fullname" value="<?= htmlspecialchars($row['fullname']) ?>" class="form-control form-control-sm">
            </td>
            <td>
                <input name="email" type="email" value="<?= htmlspecialchars($row['email']) ?>" class="form-control form-control-sm">
            </td>
            <td>
                <input name="username" value="<?= htmlspecialchars($row['username']) ?>" class="form-control form-control-sm">
            </td>
            <td>
                <select name="role" class="form-select form-select-sm">
                    <option value="Admin" <?= $row['role']=='Admin'?'selected':'' ?>>Admin</option>
                    <option value="Staff" <?= $row['role']=='Staff'?'selected':'' ?>>Staff</option>
                </select>
            </td>
            <td>
                <div class="d-flex gap-1 justify-content-center px-1">
                    <button name="update" class="btn btn-success btn-sm flex-fill">Update</button>
                    
                    <button type="button" 
                            class="btn btn-warning btn-sm flex-fill text-dark" 
                            onclick="resetPassword(<?= $row['id'] ?>, '<?= htmlspecialchars($row['username']) ?>')">
                        Reset
                    </button>

                    <button type="button" 
                            class="btn btn-danger btn-sm flex-fill" 
                            onclick="confirmDelete(<?= $row['id'] ?>)">
                        Delete
                    </button>
                </div>
            </td>
        </form>
    </tr>
<?php 
    endwhile; 
else: 
?>
    <tr>
        <td colspan="5" class="text-center py-4">
            <div class="text-muted">
                <i class="bi bi-search fs-2 d-block mb-2"></i>
                No users found matching "<strong><?= htmlspecialchars($q) ?></strong>"
            </div>
        </td>
    </tr>
<?php endif; ?>