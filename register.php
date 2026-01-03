<?php
include 'database.php';
session_start();

$message = "";

if(isset($_POST['register'])){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $role = $_POST['role'];

    // 1. Password match check
    if($password !== $confirm){
        $message = "<div class='alert alert-danger text-center'>Passwords do not match!</div>";
    } else {
        // 2. Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $message = "<div class='alert alert-danger text-center'>Username or email already exists!</div>";
        } else {
            // 3. Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname,email,username,password,role) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $fullname, $email, $username, $hashed_password, $role);
            if($stmt->execute()){
                $message = "<div class='alert alert-success text-center'>
                                Registration successful! <a href='login.php' class='alert-link'>Login here</a>
                            </div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>Error: ".$stmt->error."</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Supply Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Custom Styles -->
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2, #f6b93b, #6a82fb);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        @keyframes gradientMove {
            0%{background-position:0% 50%;}
            50%{background-position:100% 50%;}
            100%{background-position:0% 50%;}
        }

        .register-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.3);
            background-color: #ffffffdd;
            padding: 2rem;
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

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .footer-text {
            color: #ffffffcc;
        }
    </style>
</head>
<body>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">

            <div class="register-card fade-in">
                <h3 class="text-center mb-4 fw-bold">Register - Supply Inventory</h3>

                <!-- Display message -->
                <?php if($message!=""): ?>
                    <?= $message ?>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="fullname" placeholder="Full Name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" placeholder="Email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="username" placeholder="Username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <input type="password" name="confirm" placeholder="Confirm Password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <select name="role" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100 mb-2">Register</button>
                    <a href="login.php" class="btn btn-secondary w-100">Login</a>
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
