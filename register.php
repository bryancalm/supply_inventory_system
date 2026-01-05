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
    // Automatically set role to Staff for all new registrations
    $role = "Staff"; 

    if($password !== $confirm){
        $message = "<div class='alert alert-danger shadow-sm'>Passwords do not match!</div>";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $message = "<div class='alert alert-danger shadow-sm'>Username or email already exists!</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname,email,username,password,role) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $fullname, $email, $username, $hashed_password, $role);
            if($stmt->execute()){
                $message = "<div class='alert alert-success shadow-sm'>
                                Registration successful! <a href='login.php' class='alert-link'>Login here</a>
                            </div>";
            } else {
                $message = "<div class='alert alert-danger shadow-sm'>Error: ".$stmt->error."</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | Supply Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2, #f6b93b, #6a82fb);
            background-size: 400% 400%;
            animation: gradientMove 15s ease infinite;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        @keyframes gradientMove {
            0%{background-position:0% 50%;}
            50%{background-position:100% 50%;}
            100%{background-position:0% 50%;}
        }

        .register-card {
            border-radius: 1.25rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.2);
            background-color: #ffffff; /* Professional solid white */
            padding: 2.5rem;
            border: none;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px);}
            to { opacity: 1; transform: translateY(0);}
        }

        /* Styling buttons to match your system theme */
        .btn-register {
            background-color: #3e1f77; 
            border: none;
            padding: 0.8rem;
            font-weight: 600;
            color: white;
            transition: 0.3s;
        }

        .btn-register:hover {
            background-color: #2a1a4f;
            color: #ffd56a;
            transform: translateY(-1px);
        }

        .login-link {
            text-decoration: none;
            color: #3e1f77;
            font-weight: 600;
        }

        .login-link:hover {
            color: #5a2bb5;
            text-decoration: underline;
        }

        .form-floating > .form-control:focus {
            border-color: #3e1f77;
            box-shadow: 0 0 0 0.25rem rgba(62, 31, 119, 0.1);
        }
    </style>
</head>
<body>

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-9 col-md-7 col-lg-5 col-xl-4">

            <div class="register-card">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="bi bi-person-plus-fill fs-1 text-primary" style="color: #3e1f77 !important;"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Create Account</h3>
                    <p class="text-muted small">Join the Supply Inventory Management System</p>
                </div>

                <?php if($message!=""): ?>
                    <div class="mb-3"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="fullname" class="form-control" id="fName" placeholder="John Doe" required>
                        <label for="fName">Full Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="fEmail" placeholder="name@example.com" required>
                        <label for="fEmail">Email Address</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control" id="fUser" placeholder="username" required>
                        <label for="fUser">Username</label>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control" id="fPass" placeholder="Password" required>
                                <label for="fPass">Password</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="password" name="confirm" class="form-control" id="fConfirm" placeholder="Confirm" required>
                                <label for="fConfirm">Confirm Password</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn btn-register w-100 mb-3 shadow-sm rounded-pill">
                        Create Account
                    </button>
                    
                    <div class="text-center">
                        <p class="small text-muted mb-0">Already have an account?</p>
                        <a href="login.php" class="login-link small">Sign In here</a>
                    </div>
                </form>
            </div>

            <p class="text-center mt-4 text-white-50 small">
                © <?= date('Y') ?> Supply Inventory System • All Rights Reserved
            </p>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>