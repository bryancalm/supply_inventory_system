<?php
session_start();
include 'database.php';

// If already logged in, show alert and redirect
if (isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You are already logged in!');
            window.location.href = 'dashboard.php';
          </script>";
    exit;
}

$message = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, role, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Supply Inventory</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    min-height: 100vh;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-card {
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.3);
    background-color: #ffffffdd;
}
.fade-in {
    animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px);}
    to { opacity: 1; transform: translateY(0);}
}
.btn-primary {
    background-color: #1cc88a;
    border: none;
}
.btn-primary:hover {
    background-color: #17a673;
}
.btn-outline-secondary {
    border-color: #1cc88a;
    color: #1cc88a;
}
.btn-outline-secondary:hover {
    background-color: #1cc88a;
    color: #fff;
}
.footer-text {
    color: #ffffffaa;
}
</style>
</head>

<body>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">

            <div class="card login-card p-4 fade-in">
                <h3 class="text-center mb-4 fw-bold">Supply Inventory Login</h3>

                <?php if ($message): ?>
                    <div class="alert alert-danger text-center">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100 mb-2">
                        Login
                    </button>

                    <a href="register.php" class="btn btn-outline-secondary w-100">
                        Register
                    </a>
                </form>
            </div>

            <p class="text-center mt-3 footer-text small">
                © <?= date('Y') ?> Supply Inventory System
            </p>

        </div>
    </div>
</div>

</body>
</html>
