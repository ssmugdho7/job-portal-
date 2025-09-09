<?php
require_once 'db.php';
include("header.php");
// if (isset($_SESSION['user_id'])) {
//     header("Location: /index.php");
//     exit;
// }

$error = '';
$success = '';

$user_type = isset($_GET['type']) && in_array($_GET['type'], ['employee', 'employer']) 
    ? $_GET['type'] 
    : 'employee';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = $_POST['user_type'];
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Connect to database
       
        // Check if username or email already exists
        $sql = "SELECT COUNT(*) FROM `users` WHERE `username` = '$username' OR `email` = '$email'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        
        if ($row[0] > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Create new user (without password hashing)
            $sql = "INSERT INTO users (username, email, password, user_type) 
                    VALUES ('$username', '$email', '$password', '$user_type')";
            
            if (mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);
                
                // Create profile based on user type
                if ($user_type == 'employee') {
                    $sql = "INSERT INTO employee_profiles (user_id, first_name, last_name) 
                            VALUES ('$user_id', '', '')";
                } elseif ($user_type == 'employer') {
                    $sql = "INSERT INTO employer_profiles (user_id, company_name) 
                            VALUES ('$user_id', '')";
                }
                
                mysqli_query($conn, $sql);
                $success = 'Registration successful! Please login.';
                header("Refresh: 2; url=login.php");
            } else {
                $error = 'Error: ' . mysqli_error($conn);
            }
        }
        
        mysqli_close($conn);
    }
}
?>




<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Create Your Account</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php else: ?>
                        <form action="register.php" method="POST">
                            <input type="hidden" name="user_type" value="<?= $user_type ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username*</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email*</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password* (min 6 characters)</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirm Password*</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-1">Registering as:</p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type_radio" id="employeeType" 
                                           value="employee" <?= $user_type == 'employee' ? 'checked' : '' ?> >
                                    <label class="form-check-label" for="employeeType">Job Seeker</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type_radio" id="employerType" 
                                           value="employer" <?= $user_type == 'employer' ? 'checked' : '' ?> >
                                    <label class="form-check-label" for="employerType">Employer</label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <p>Already have an account? <a href="login.php">Login here</a></p>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>