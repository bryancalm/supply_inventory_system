<?php
include 'database.php';
session_start();

$message = "";

if(isset($_POST['register'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $confirm = $_POST['confirm'];
    $role = $_POST['role'];

    if($_POST['password'] !== $confirm){
        $message = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (fullname,email,username,password,role) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss",$fullname,$email,$username,$password,$role);
        if($stmt->execute()){
            $message = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Supply Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Register</h3>
    <?php if($message!="") echo "<div class='alert alert-info'>$message</div>"; ?>
    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" class="form-control mb-2" required>
        <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
        <input type="text" name="username" placeholder="Username" class="form-control mb-2" required>
        <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
        <input type="password" name="confirm" placeholder="Confirm Password" class="form-control mb-2" required>
        <select name="role" class="form-control mb-2" required>
            <option value="">Select Role</option>
            <option value="Admin">Admin</option>
            <option value="Staff">Staff</option>
        </select>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-secondary">Login</a>
    </form>
</div>
</body>
</html>
