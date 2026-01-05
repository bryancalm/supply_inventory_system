<?php
session_start();
include '../database.php';

<<<<<<< HEAD
// ADMIN ONLY CHECK
=======
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit;
}

$q = $_GET['q'] ?? '';
$like = "%$q%";

$stmt = $conn->prepare(
<<<<<<< HEAD
    "SELECT * FROM users 
     WHERE fullname LIKE ? OR email LIKE ? OR username LIKE ? 
=======
    "SELECT * FROM users
     WHERE fullname LIKE ? OR email LIKE ? OR username LIKE ?
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
     ORDER BY fullname ASC"
);
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

<<<<<<< HEAD
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
=======
while ($row = $result->fetch_assoc()):
?>
<form method="POST" action="dashboard.php?page=users">
<tr>
    <td>
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input name="fullname" value="<?= $row['fullname'] ?>" class="form-control">
    </td>
    <td>
        <input name="email" type="email" value="<?= $row['email'] ?>" class="form-control">
    </td>
    <td>
        <input name="username" value="<?= $row['username'] ?>" class="form-control">
    </td>
    <td>
        <select name="role" class="form-control">
            <option value="Admin" <?= $row['role']=='Admin'?'selected':'' ?>>Admin</option>
            <option value="Staff" <?= $row['role']=='Staff'?'selected':'' ?>>Staff</option>
        </select>
    </td>
    <td class="text-center">
        <button name="update" class="btn btn-success btn-sm">Update</button>
        <a href="dashboard.php?page=users&delete=<?= $row['id'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Delete this user?')">
           Delete
        </a>
    </td>
</tr>
</form>
<?php endwhile; ?>
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
