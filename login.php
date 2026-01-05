<?php
session_start();
include 'database.php';

<<<<<<< HEAD
// If already logged in, redirect
=======
// If already logged in, redirect to dashboard
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
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
<<<<<<< HEAD
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
=======
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        // Redirect to unified dashboard
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<<<<<<< HEAD
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Supply Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-card {
            border-radius: 1.25rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.3);
            background-color: #ffffff;
            padding: 2.5rem;
            border: none;
        }

        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }

        /* Branding consistency */
        .btn-login {
            background-color: #3e1f77; 
            border: none;
            padding: 0.8rem;
            font-weight: 600;
            color: white;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #2a1a4f;
            color: #ffd56a;
            transform: translateY(-1px);
        }

        /* Password Toggle Styling */
        .password-container { position: relative; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            color: #6c757d;
            border: none;
            background: none;
            padding: 0;
        }

        .register-link { text-decoration: none; color: #3e1f77; font-weight: 600; }
        .form-floating > .form-control:focus { border-color: #3e1f77; box-shadow: 0 0 0 0.25rem rgba(62, 31, 119, 0.1); }
    </style>
</head>

<body>

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-9 col-md-7 col-lg-5 col-xl-4">

            <div class="login-card">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="bi bi-shield-lock-fill fs-1" style="color: #3e1f77;"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Portal Login</h3>
                    <p class="text-muted small">Access the Supply Inventory System</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-danger text-center small py-2 shadow-sm">
                        <i class="bi bi-exclamation-circle me-2"></i><?= $message ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control" id="userInput" placeholder="Username" required>
                        <label for="userInput">Username</label>
                    </div>

                    <div class="password-container form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="passInput" placeholder="Password" required>
                        <label for="passInput">Password</label>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                    </div>

                    <button type="submit" name="login" class="btn btn-login w-100 mb-3 shadow-sm rounded-pill">
                        Sign In
                    </button>

                    <div class="text-center mt-3">
                        <p class="small text-muted mb-0">New user?</p>
                        <a href="register.php" class="register-link small">Create an account</a>
                    </div>
                </form>
            </div>

            <p class="text-center mt-4 text-white-50 small">
                © <?= date('Y') ?> Supply Inventory System • All Rights Reserved
            </p>

        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passInput = document.getElementById('passInput');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passInput.type === 'password') {
            passInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-fill');
            toggleIcon.classList.add('bi-eye-slash-fill');
        } else {
            passInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash-fill');
            toggleIcon.classList.add('bi-eye-fill');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
=======
<html>
<head>
    <title>Login - Supply Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width:400px;">
    <h3 class="text-center mb-3">Login</h3>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        <a href="register.php" class="btn btn-secondary w-100 mt-2">Register</a>
    </form>
</div>

</body>
</html>
>>>>>>> 8950efdb46d49b2ebfdc5f6dc576dfb15f16179f
