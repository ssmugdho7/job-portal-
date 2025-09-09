<?php
session_start();
require("../db.php");
// Basic session check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    header("Location: ../index.php");
    exit;
}


$employer_id = $_SESSION['user_id'];
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query
$sql = "SELECT a.*, j.title, u.email, ep.first_name, ep.last_name, ep.phone 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN users u ON a.employee_id = u.id 
        JOIN employee_profiles ep ON u.id = ep.user_id
        WHERE j.employer_id = $employer_id";

if ($job_id) {
    $sql .= " AND a.job_id = $job_id";
}

if ($status_filter != 'all') {
    $sql .= " AND a.status = '$status_filter'";
}

$sql .= " ORDER BY a.applied_at DESC";

$result = mysqli_query($conn, $sql);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get job details if filtered by job
$job = null;
if ($job_id) {
    $job_result = mysqli_query($conn, "SELECT * FROM jobs WHERE id = $job_id AND employer_id = $employer_id");
    $job = mysqli_fetch_assoc($job_result);
}

// Get all employer's jobs for dropdown
$jobs_result = mysqli_query($conn, "SELECT id, title FROM jobs WHERE employer_id = $employer_id ORDER BY posted_at DESC");
$jobs = mysqli_fetch_all($jobs_result, MYSQLI_ASSOC);

$pageTitle = "Manage Applicants";
include '../header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Job Applicants</h1>
        <?php if ($job): ?>
            <a href="./view_jobs.php" class="btn btn-outline-primary">Back to All Jobs</a>
        <?php endif; ?>
    </div>
    
    <?php if ($job): ?>
        <div class="alert alert-info mb-4">
            <h5>Showing applicants for: <?php echo $job['title'] ?></h5>
            <p class="mb-0"><?php echo $job['description'] ?></p>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-6">
                    <form class="d-flex">
                        <select name="job_id" class="form-select me-2" onchange="this.form.submit()">
                            <option value="">All Jobs</option>
                            <?php foreach ($jobs as $j): ?>
                                <option value="<?php echo $j['id'] ?>" <?php echo $job_id == $j['id'] ? 'selected' : '' ?>>
                                    <?php echo $j['title'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="reviewed" <?php echo $status_filter == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                            <option value="selected" <?php echo $status_filter == 'selected' ? 'selected' : '' ?>>Selected</option>
                            <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="applicants.php?export=csv&job_id=<?php echo $job_id ?>&status=<?php echo $status_filter ?>" class="btn btn-outline-secondary">
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Job Title</th>
                            <th>Applied</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $app['first_name'] . ' ' . $app['last_name'] ?></strong><br>
                                    <small class="text-muted"><?php echo $app['email'] ?></small>
                                </td>
                                <td><?php echo $app['title'] ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                <td>
                                    <?php echo $app['phone'] ?><br>
                                    <a href="mailto:<?php echo $app['email'] ?>">Email</a>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $app['status'] == 'selected' ? 'success' : 
                                        ($app['status'] == 'rejected' ? 'danger' : 'warning') 
                                    ?>">
                                        <?php echo ucfirst($app['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="./view_applications.php?id=<?php echo $app['id'] ?>">View Application</a></li>
                                            <li><a class="dropdown-item" href="<?php echo $app['cv_path'] ?>" target="_blank">View CV</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="update_application_status.php" method="POST" class="dropdown-item">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id'] ?>">
                                                    <input type="hidden" name="status" value="selected">
                                                    <button type="submit" class="btn btn-link p-0 text-success">Mark as Selected</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="update_application_status.php" method="POST" class="dropdown-item">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id'] ?>">
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-link p-0 text-danger">Reject Application</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (empty($applications)): ?>
                <div class="alert alert-info">
                    No applicants found matching your criteria.
                    <?php if ($job_id || $status_filter != 'all'): ?>
                        <a href="./applicants.php" class="alert-link">Clear filters</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>