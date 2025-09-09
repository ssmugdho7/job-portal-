<?php
session_start();
require("../db.php");

// Basic session check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    header("Location: ../index.php");
    exit;
}


$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $company_name = trim($_POST['company_name']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $location = trim($_POST['location']);
    $salary = trim($_POST['salary']);
    $type = $_POST['type'];
    $category = $_POST['category'];
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : NULL;
    
    if (empty($title) || empty($description) || empty($requirements) || empty($location)) {
        $error = 'Please fill in all required fields.';
    } else {
        $query = "INSERT INTO jobs (employer_id, title, company_name,description, requirements, location, salary, type, category, deadline) 
                 VALUES ('{$_SESSION['user_id']}', '$title', '$company_name' ,'$description', '$requirements', '$location', 
                         '$salary', '$type', '$category', " . ($deadline ? "'$deadline'" : "NULL") . ")";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Job posted successfully!';
            header("Refresh: 2; url=dashboard.php");
        } else {
            $error = 'Error: ' . mysqli_error($conn);
        }
    }
}

$pageTitle = "Post New Job";
include '../header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Post a New Job</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Job Title*</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="company_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Job Description*</label>
                                <textarea class="form-control" name="description" rows="5" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Requirements*</label>
                                <textarea class="form-control" name="requirements" rows="5" required></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Location*</label>
                                    <input type="text" class="form-control" name="location" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Salary</label>
                                    <input type="text" class="form-control" name="salary" placeholder="e.g., $50,000 - $70,000">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Job Type*</label>
                                    <select class="form-select" name="type" required>
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category*</label>
                                    <input type="text" class="form-control" name="category" required placeholder="e.g., Software Development, Marketing">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Application Deadline</label>
                                <input type="date" class="form-control" name="deadline">
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Post Job</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>