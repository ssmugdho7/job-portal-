<?php
session_start();
require("../db.php");
include("../header.php");

// Simple auth check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: /index.php");
    exit;
}

// Handle withdrawal if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw_application'])) {
    $application_id = $_POST['application_id'];
    $employee_id = $_SESSION['user_id'];
    
    // Verify the application belongs to this user before deleting
    $check_sql = "SELECT id FROM applications WHERE id = $application_id AND employee_id = $employee_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result)) {
        $delete_sql = "DELETE FROM applications WHERE id = $application_id";
        if (mysqli_query($conn, $delete_sql)) {
            $_SESSION['message'] = "Application withdrawn successfully";
        } else {
            $_SESSION['message'] = "Error withdrawing application";
        }
    }
    
    // Redirect to same page to prevent form resubmission
    header("Location: applications.php");
    exit;
}

$employee_id = $_SESSION['user_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query
$sql = "SELECT a.*, j.title, ep.company_name 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN employer_profiles ep ON j.employer_id = ep.user_id
        WHERE a.employee_id = $employee_id";

if ($filter == 'selected') {
    $sql .= " AND a.status = 'selected'";
} elseif ($filter == 'rejected') {
    $sql .= " AND a.status = 'rejected'";
} elseif ($filter == 'pending') {
    $sql .= " AND a.status = 'pending'";
}

$sql .= " ORDER BY a.applied_at DESC";

$result = mysqli_query($conn, $sql);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Job Applications</h1>
        <div>
            <a href="jobs.php" class="btn btn-primary">Browse Jobs</a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0">Application History</h5>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="?filter=all" class="btn btn-outline-secondary <?= $filter == 'all' ? 'active' : '' ?>">All</a>
                        <a href="?filter=selected" class="btn btn-outline-secondary <?= $filter == 'selected' ? 'active' : '' ?>">Selected</a>
                        <a href="?filter=pending" class="btn btn-outline-secondary <?= $filter == 'pending' ? 'active' : '' ?>">Pending</a>
                        <a href="?filter=rejected" class="btn btn-outline-secondary <?= $filter == 'rejected' ? 'active' : '' ?>">Rejected</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (isset($_GET['applied'])): ?>
                <div class="alert alert-success">Your application has been submitted successfully!</div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= $app['title'] ?></td>
                                <td><?= $app['company_name'] ?></td>
                                <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $app['status'] == 'selected' ? 'success' : 
                                        ($app['status'] == 'rejected' ? 'danger' : 'warning') 
                                    ?>">
                                        <?= ucfirst($app['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($app['status'] == 'pending'): ?>
                                        <form method="POST" class="d-inline">
                                        <td> 

                                            <a href="view_job.php?id=<?= $app['job_id'] ?>" class="btn btn-sm btn-outline-primary me-2">View Job</a>
                                            <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                            <button type="submit" name="withdraw_application" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to withdraw this application?')">
                                                Withdraw
                                            </button>
                                        </td>    
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (empty($applications)): ?>
                <div class="alert alert-info">You haven't applied to any jobs yet.</div>
                <div class="text-center">
                    <a href="jobs.php" class="btn btn-primary">Browse Available Jobs</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include("../footer.php") ?>