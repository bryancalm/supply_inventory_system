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

    // Changed to JS Redirect to avoid "Header already sent" error
    echo "<script>window.location.href='dashboard.php?page=users&status=added';</script>";
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

    // Changed to JS Redirect
    echo "<script>window.location.href='dashboard.php?page=users&status=updated';</script>";
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

    // Changed to JS Redirect
    echo "<script>window.location.href='dashboard.php?page=users&status=reset';</script>";
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

    // Changed to JS Redirect
    echo "<script>window.location.href='dashboard.php?page=users&status=deleted';</script>";
    exit;
}

/* ======================
   FETCH USERS
====================== */
$stmt = $conn->prepare("SELECT * FROM users ORDER BY fullname ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h4 class="mb-3">User Management</h4>

<div class="card mb-4">
    <div class="card-body">
        <h6 class="mb-3 fw-bold">Add New User</h6>
        <form method="POST" class="row g-3"> <div class="col-12 col-md-6 col-lg-3">
                <label class="small fw-bold">Full Name</label>
                <input name="fullname" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="small fw-bold">Email</label>
                <input name="email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="small fw-bold">Username</label>
                <input name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="small fw-bold">Password</label>
                <input name="password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="col-12 col-md-12 col-lg-2">
                <label class="small fw-bold">Role</label>
                <select name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>
            <div class="col-12 text-end mt-3">
                <button name="add" class="btn btn-primary w-100 w-md-auto">Add User</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <input type="text" id="search" class="form-control" placeholder="Search users...">
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive"> <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="min-width: 150px;">Full Name</th>
                        <th style="min-width: 150px;">Email</th>
                        <th style="min-width: 120px;">Username</th>
                        <th style="min-width: 100px;">Role</th>
                        <th style="min-width: 250px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <?php while ($row = $result->fetch_assoc()): ?>
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
                                <div class="d-flex gap-1 justify-content-center">
                                    <button name="update" class="btn btn-success btn-sm flex-fill">Update</button>
                                    <button type="button" class="btn btn-warning btn-sm flex-fill text-dark" 
                                            onclick="resetPassword(<?= $row['id'] ?>, '<?= htmlspecialchars($row['username']) ?>')">
                                        Reset
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm flex-fill" 
                                            onclick="confirmDelete(<?= $row['id'] ?>)">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
            window.location.href = "dashboard.php?page=users&delete=" + id;
        }
    });
}

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
                Swal.showValidationMessage('Password must be at least 6 characters');
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

<script>
const searchInput = document.getElementById("search");
const usersTable  = document.getElementById("usersTable");

searchInput.addEventListener("keyup", function () {
    fetch("admin/users_search.php?q=" + encodeURIComponent(this.value))
        .then(res => res.text())
        .then(data => usersTable.innerHTML = data);
});
</script>
<style>
/* Make form controls smaller in the table for better fit */
.table .form-control-sm, .table .form-select-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Ensure action buttons don't get too small */
.btn-sm {
    white-space: nowrap;
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
}

/* On mobile, allow the action div to wrap if necessary */
@media (max-width: 576px) {
    .d-flex.gap-1 {
        flex-wrap: wrap;
    }
    .flex-fill {
        flex: 1 1 auto;
        min-width: 70px;
    }
}
</style>