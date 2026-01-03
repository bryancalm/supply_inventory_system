<?php
session_start();
include '../database.php';

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
