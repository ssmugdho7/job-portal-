<?php
// application_details.php
session_start();
require("../db.php");
include("../header.php");

// Check if user is logged in and is an employer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    header("Location: ../login.php");
    exit();
}

// Get application ID from URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$application_id = mysqli_real_escape_string($conn, $_GET['id']);

// Query to get application details
$query = "SELECT 
            a.*, 
            j.title AS job_title, 
            j.company_name, 
            e.first_name, 
            e.last_name, 
            e.phone, 
            e.address, 
            e.bio, 
            e.skills, 
            e.education, 
            e.experience, 
            e.cv_path,
            u.email
          FROM applications a
          JOIN jobs j ON a.job_id = j.id
          JOIN employee_profiles e ON a.employee_id = e.user_id
          JOIN users u ON e.user_id = u.id
          WHERE a.id = '$application_id' 
          AND j.employer_id = '{$_SESSION['user_id']}'";

$result = mysqli_query($conn, $query);
$application = mysqli_fetch_assoc($result);

if (!$application) {
    header("Location: dashboard.php");
    exit();
}

// Update application status if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $update_query = "UPDATE applications SET status = '$new_status' WHERE id = '$application_id'";
    mysqli_query($conn, $update_query);
    header("Location: view_applications.php");
    exit();
}


?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Application for: <?= htmlspecialchars($application['job_title']) ?></h3>
                    <p class="mb-0">Company: <?= htmlspecialchars($application['company_name']) ?></p>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4>Applicant Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?= htmlspecialchars($application['first_name'] . ' ' . $application['last_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($application['phone']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($application['address'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4>Professional Summary</h4>
                        <p><?= nl2br(htmlspecialchars($application['bio'])) ?></p>
                    </div>

                    <div class="mb-4">
                        <h4>Skills</h4>
                        <p><?= nl2br(htmlspecialchars($application['skills'])) ?></p>
                    </div>

                    <div class="mb-4">
                        <h4>Education</h4>
                        <p><?= nl2br(htmlspecialchars($application['education'])) ?></p>
                    </div>

                    <div class="mb-4">
                        <h4>Experience</h4>
                        <p><?= nl2br(htmlspecialchars($application['experience'])) ?></p>
                    </div>

                    <div class="mb-4">
                        <h4>Cover Letter</h4>
                        <div class="border p-3 bg-light">
                            <?= nl2br(htmlspecialchars($application['cover_letter'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h4>Application Details</h4>
                </div>
                <div class="card-body">
                    <p><strong>Applied on:</strong> <?= date('M d, Y h:i A', strtotime($application['applied_at'])) ?></p>
                    <p><strong>Current Status:</strong> 
                        <span class="badge 
                            <?= $application['status'] == 'selected' ? 'bg-success' : 
                               ($application['status'] == 'rejected' ? 'bg-danger' : 
                               ($application['status'] == 'reviewed' ? 'bg-info' : 'bg-warning')) ?>">
                            <?= ucfirst($application['status']) ?>
                        </span>
                    </p>

                    <div class="mt-4">
                        <h5>Update Status</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <select class="form-select" name="status">
                                    <option value="pending" <?= $application['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="reviewed" <?= $application['status'] == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                    <option value="selected" <?= $application['status'] == 'selected' ? 'selected' : '' ?>>Selected</option>
                                    <option value="rejected" <?= $application['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h4>Applicant's CV</h4>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($application['cv_path'])): ?>
                        <div class="mb-3">
                            <a href="<?= htmlspecialchars($application['cv_path']) ?>" class="btn btn-primary" target="_blank">
                                <i class="bi bi-download"></i> Download CV
                            </a>
                        </div>
                        <iframe src="<?= htmlspecialchars($application['cv_path']) ?>" style="width:100%; height:400px; border:1px solid #ddd;"></iframe>
                    <?php else: ?>
                        <p class="text-muted">No CV uploaded</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="./dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php include("../footer.php"); ?>