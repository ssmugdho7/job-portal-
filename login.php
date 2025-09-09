<?php
session_start();
require("db.php");
include("header.php");
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM `users` WHERE `username`='$username' OR `email`='$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
     
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            

            if ($user['user_type'] == 'admin') {
                header("Location: admin/dashboard.php");
            
            } elseif ($user['user_type'] == 'employer') {
                header("Location: employer/dashboard.php");
            } else {
                header("Location: employee/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>





<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <div class="mt-3 text-center">
                <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</div>
</body>

<?php
include("footer.php");
?>