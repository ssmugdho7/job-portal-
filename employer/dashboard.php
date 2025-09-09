<?php
require("../db.php");
session_start();

// Basic session check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    header("Location: ../index.php");
    exit;
}



$employer_id = $_SESSION['user_id'];



// Initialize counts
$jobs_count = 0;
$active_jobs_count = 0;
$applications_count = 0;

// Get total jobs count
$query = "SELECT COUNT(*) FROM jobs WHERE employer_id = $employer_id";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_row($result);
    $jobs_count = $row[0];
}

// Get active jobs count
$query = "SELECT COUNT(*) FROM jobs WHERE employer_id = $employer_id AND is_active = 1 AND (deadline IS NULL OR deadline >= CURDATE())";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_row($result);
    $active_jobs_count = $row[0];
}

// Get applications count
$query = "SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = $employer_id";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_row($result);
    $applications_count = $row[0];
}

$pageTitle = "Employer Dashboard";
include '../header.php';

// Get employer profile (assuming this function exists and works)
$profile = array('company_name' => 'Test Company'); // Temporary placeholder
// $profile = getEmployerProfile($employer_id); // Uncomment when you have this function
?>



<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Welcome</h1>
            <p class="lead">Manage your job postings and applicants from your dashboard.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="post_job.php" class="btn btn-primary">Post New Job</a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs Posted</h5>
                    <p class="card-text display-4"><?= $jobs_count ?></p>
                    <a href="/job-portal2/employer/jobs.php" class="text-white">View Jobs</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title">Active Jobs</h5>
                    <p class="card-text display-4"><?= $active_jobs_count ?></p>
                    <a href="/job-portal2/employer/jobs.php?filter=active" class="text-white">View Active</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Applications</h5>
                    <p class="card-text display-4"><?= $applications_count ?></p>
                    <a href="applicants.php" class="text-white">View Applicants</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5>Recent Job Postings</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $recent_jobs = $conn->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY posted_at DESC LIMIT 5");
                    $recent_jobs->bind_param("i", $employer_id);
                    $recent_jobs->execute();
                    $result = $recent_jobs->get_result();
                    ?>
                    <ul class="list-group list-group-flush">
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6><?= htmlspecialchars($job['title']) ?></h6>
                                    <small class="text-muted">Posted: <?= date('M d, Y', strtotime($job['posted_at'])) ?></small>
                                </div>
                                <span class="badge bg-<?= $job['is_active'] ? 'success' : 'secondary' ?> rounded-pill">
                                    <?= $job['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                        <a href="/job-portal2/employer/jobs.php" class="btn btn-sm btn-outline-primary mt-3">View All Jobs</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5>Recent Applications</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                    $recent_apps = $conn->prepare("SELECT a.*, j.title, u.email, ep.first_name, ep.last_name 
                                                   FROM applications a 
                                                   JOIN jobs j ON a.job_id = j.id 
                                                   JOIN users u ON a.employee_id = u.id 
                                                   JOIN employee_profiles ep ON u.id = ep.user_id
                                                   WHERE j.employer_id = ?
                                                   ORDER BY a.applied_at DESC LIMIT 5");
                    $recent_apps->bind_param("i", $employer_id);
                    $recent_apps->execute();
                    $result = $recent_apps->get_result();
                    ?>
                    <ul class="list-group list-group-flush">
                        <?php while ($app = $result->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6><?= htmlspecialchars($app['title']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></small>
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
                        <a href="applicants.php" class="btn btn-sm btn-outline-primary mt-3">View All Applicants</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('../footer.php'); ?>