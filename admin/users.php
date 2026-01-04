<?php
// ADMIN ONLY
if ($_SESSION['role'] != 'Admin') {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    exit;
}

/* ======================
   ADD USER
====================== */
if (isset($_POST['add'])) {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $stmt = $conn->prepare(
        "INSERT INTO users (fullname, email, username, password, role)
         VALUES (?,?,?,?,?)"
    );
    $stmt->bind_param("sssss", $fullname, $email, $username, $password, $role);
    $stmt->execute();

    header("Location: dashboard.php?page=users&status=added");
    exit;
}

/* ======================
   UPDATE USER
====================== */
if (isset($_POST['update'])) {
    $id       = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $role     = $_POST['role'];

    $stmt = $conn->prepare(
        "UPDATE users
         SET fullname=?, email=?, username=?, role=?
         WHERE id=?"
    );
    $stmt->bind_param("ssssi", $fullname, $email, $username, $role, $id);
    $stmt->execute();

    header("Location: dashboard.php?page=users&status=updated");
    exit;
}

/* ======================
   RESET PASSWORD
====================== */
if (isset($_POST['reset_password'])) {
    $id       = $_POST['id'];
    $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "UPDATE users SET password=? WHERE id=?"
    );
    $stmt->bind_param("si", $password, $id);
    $stmt->execute();

    header("Location: dashboard.php?page=users&status=reset");
    exit;
}

/* ======================
   DELETE USER
====================== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: dashboard.php?page=users&status=deleted");
    exit;
}

/* ======================
   FETCH USERS
====================== */
$stmt = $conn->prepare("SELECT * FROM users ORDER BY fullname ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h4 class="mb-3">User Management</h4>

<!-- ADD USER -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="mb-3">Add User</h6>
        <form method="POST" class="row g-2">
            <div class="col-md-3">
                <input name="fullname" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="col-md-3">
                <input name="email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-2">
                <input name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="col-md-2">
                <input name="password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-control" required>
                    <option value="">Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>
            <div class="col-md-12 text-end">
                <button name="add" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

<!-- SEARCH -->
<div class="card mb-3">
    <div class="card-body">
        <input type="text" id="search" class="form-control" placeholder="Search users...">
    </div>
</div>

<!-- USERS TABLE -->
<div class="card">
<div class="card-body p-0">
<table class="table table-bordered table-hover mb-0">
<thead class="table-dark">
<tr>
    <th>Full Name</th>
    <th>Email</th>
    <th>Username</th>
    <th>Role</th>
    <th width="220">Action</th>
</tr>
</thead>
<tbody id="usersTable">
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<form method="POST">
    <td>
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input name="fullname" value="<?= htmlspecialchars($row['fullname']) ?>" class="form-control">
    </td>
    <td>
        <input name="email" type="email" value="<?= htmlspecialchars($row['email']) ?>" class="form-control">
    </td>
    <td>
        <input name="username" value="<?= htmlspecialchars($row['username']) ?>" class="form-control">
    </td>
    <td>
        <select name="role" class="form-control">
            <option value="Admin" <?= $row['role']=='Admin'?'selected':'' ?>>Admin</option>
            <option value="Staff" <?= $row['role']=='Staff'?'selected':'' ?>>Staff</option>
        </select>
    </td>
    <td class="text-center">
        <button name="update" class="btn btn-success btn-sm mb-1">Update</button>

        <button type="button"
                class="btn btn-warning btn-sm mb-1"
                onclick="resetPassword(<?= $row['id'] ?>, '<?= htmlspecialchars($row['username']) ?>')">
            Reset Password
        </button>

        <button type="button"
                class="btn btn-danger btn-sm"
                onclick="confirmDelete(<?= $row['id'] ?>)">
            Delete
        </button>
    </td>
</form>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

<!-- DELETE CONFIRM -->
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This user will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                "dashboard.php?page=users&delete=" + id;
        }
    });
}
</script>

<!-- RESET PASSWORD MODAL -->
<script>
function resetPassword(id, username) {
    Swal.fire({
        title: 'Reset Password',
        text: 'New password for ' + username,
        input: 'password',
        inputPlaceholder: 'Enter new password',
        showCancelButton: true,
        confirmButtonText: 'Reset',
        confirmButtonColor: '#f0ad4e',
        preConfirm: (password) => {
            if (!password || password.length < 6) {
                Swal.showValidationMessage(
                    'Password must be at least 6 characters'
                );
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';

            form.innerHTML = `
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="new_password" value="${result.value}">
                <input type="hidden" name="reset_password" value="1">
            `;

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<!-- STATUS ALERT -->
<?php if (isset($_GET['status'])): ?>
<script>
<?php if ($_GET['status'] == 'added'): ?>
Swal.fire({ icon:'success', title:'User Added', timer:1500, showConfirmButton:false });
<?php elseif ($_GET['status'] == 'updated'): ?>
Swal.fire({ icon:'success', title:'User Updated', timer:1500, showConfirmButton:false });
<?php elseif ($_GET['status'] == 'deleted'): ?>
Swal.fire({ icon:'success', title:'User Deleted', timer:1500, showConfirmButton:false });
<?php elseif ($_GET['status'] == 'reset'): ?>
Swal.fire({ icon:'success', title:'Password Reset Successfully', timer:1500, showConfirmButton:false });
<?php endif; ?>
</script>
<?php endif; ?>

<!-- LIVE SEARCH -->
<script>
const searchInput = document.getElementById("search");
const usersTable  = document.getElementById("usersTable");

searchInput.addEventListener("keyup", function () {
    fetch("admin/users_search.php?q=" + encodeURIComponent(this.value))
        .then(res => res.text())
        .then(data => usersTable.innerHTML = data);
});
</script>
