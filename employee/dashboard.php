<?php
session_start();
require_once '../db.php'; // MySQLi connection file
include '../header.php';

// Check if user is logged in as employee
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: ../index.php");
    exit;
}

$employee_id = $_SESSION['user_id'];

// Get counts for dashboard
$applications_count = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM applications WHERE employee_id = $employee_id"))[0];

$selected_count = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM applications WHERE employee_id = $employee_id AND status = 'selected'"))[0];

$active_jobs_count = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM jobs WHERE is_active = 1 AND (deadline IS NULL OR deadline >= CURDATE())"))[0];

// Get employee profile
$profile = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM employee_profiles WHERE user_id = $employee_id"));

// Get recent jobs
$recent_jobs = mysqli_query($conn, 
    "SELECT j.*, ep.company_name 
     FROM jobs j 
     JOIN employer_profiles ep ON j.employer_id = ep.user_id
     WHERE j.is_active = 1 AND (j.deadline IS NULL OR j.deadline >= CURDATE())
     ORDER BY j.posted_at DESC LIMIT 5");

// Get recent applications
$recent_apps = mysqli_query($conn, 
    "SELECT a.*, j.title, ep.company_name 
     FROM applications a 
     JOIN jobs j ON a.job_id = j.id 
     JOIN employer_profiles ep ON j.employer_id = ep.user_id
     WHERE a.employee_id = $employee_id
     ORDER BY a.applied_at DESC LIMIT 5");
?>


       
    <div class="row mb-4">
            <div class="col-md-8">
                <h1>Welcome <?= htmlspecialchars($profile['first_name'] ?? 'Employee') ?></h1>
                <p class="lead">Find your next career opportunity.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="/job-portal2/employee/build_cv.html" class="btn btn-primary">Build Your CV</a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Applications</h5>
                        <p class="card-text display-4"><?= $applications_count ?></p>
                        <a href="./applications.php" class="text-white">View Applications</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Selected</h5>
                        <p class="card-text display-4"><?= $selected_count ?></p>
                        <a href="./applications.php?filter=selected" class="text-white">View Selected</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Active Jobs</h5>
                        <p class="card-text display-4"><?= $active_jobs_count ?></p>
                        <a href="jobs.php" class="text-white">Browse Jobs</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Jobs & Applications -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Recent Jobs</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($job = mysqli_fetch_assoc($recent_jobs)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6><?= htmlspecialchars($job['title']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($job['company_name']) ?></small>
                                    </div>
                                    <a href="view_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <a href="jobs.php" class="btn btn-sm btn-outline-primary mt-3">View All Jobs</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Applications</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($app = mysqli_fetch_assoc($recent_apps)): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6><?= htmlspecialchars($app['title']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($app['company_name']) ?></small>
                                        </div>
                                        <span class="badge bg-<?= 
                                            $app['status'] == 'selected' ? 'success' : 
                                            ($app['status'] == 'rejected' ? 'danger' : 'warning') 
                                        ?>">
                                            <?= ucfirst($app['status']) ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <a href="./applications.php" class="btn btn-sm btn-outline-primary mt-3">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>