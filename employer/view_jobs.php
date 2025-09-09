<?php
session_start();
require("../db.php");

// Basic session check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    header("Location: ../index.php.php"); 
    exit;
}


$employer_id = $_SESSION['user_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$sql = "SELECT * FROM jobs WHERE employer_id = $employer_id";

if ($filter == 'active') {
    $sql .= " AND is_active = 1 AND (deadline IS NULL OR deadline >= CURDATE())";
} elseif ($filter == 'expired') {
    $sql .= " AND deadline < CURDATE()";
} elseif ($filter == 'inactive') {
    $sql .= " AND is_active = 0";
}

if (!empty($search)) {
    $search_term = "%$search%";
    $sql .= " AND (title LIKE '$search_term' OR description LIKE '$search_term')";
}

$sql .= " ORDER BY posted_at DESC";

$result = mysqli_query($conn, $sql);
$jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);

$pageTitle = "Manage Jobs";
include '../header.php';

// Get employer profile (simplified)
$profile_result = mysqli_query($conn, "SELECT company_name FROM employer_profiles WHERE user_id = $employer_id");
$profile = mysqli_fetch_assoc($profile_result);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Your Job Postings</h1>
        <div>
            <a href="post_job.php" class="btn btn-primary">Post New Job</a>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-6">
                    <form class="d-flex" method="GET">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search your jobs..." value="<?php echo $search ?>">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="?filter=all" class="btn btn-outline-secondary <?php echo $filter == 'all' ? 'active' : '' ?>">All</a>
                        <a href="?filter=active" class="btn btn-outline-secondary <?php echo $filter == 'active' ? 'active' : '' ?>">Active</a>
                        <a href="?filter=expired" class="btn btn-outline-secondary <?php echo $filter == 'expired' ? 'active' : '' ?>">Expired</a>
                        <a href="?filter=inactive" class="btn btn-outline-secondary <?php echo $filter == 'inactive' ? 'active' : '' ?>">Inactive</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Posted</th>
                            <th>Deadline</th>
                            <th>Applications</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo $job['title'] ?></td>
                                <td><?php echo date('M d, Y', strtotime($job['posted_at'])) ?></td>
                                <td><?php echo $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : 'None' ?></td>
                                <td>
                                    <?php 
                                    $count_result = mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE job_id = {$job['id']}");
                                    $count = mysqli_fetch_row($count_result)[0];
                                    ?>
                                    <a href="applicants.php?job_id=<?php echo $job['id'] ?>"><?php echo $count ?> applicant(s)</a>
                                </td>
                                <td>
                                    <?php if (!$job['is_active']): ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php elseif ($job['deadline'] && $job['deadline'] < date('Y-m-d')): ?>
                                        <span class="badge bg-danger">Expired</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view_job.php?id=<?php echo $job['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="edit_job.php?id=<?php echo $job['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="toggle_job.php" method="POST" class="d-inline">
                                        <input type="hidden" name="job_id" value="<?php echo $job['id'] ?>">
                                        <input type="hidden" name="current_status" value="<?php echo $job['is_active'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-<?php echo $job['is_active'] ? 'warning' : 'success' ?>">
                                            <?php echo $job['is_active'] ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (empty($jobs)): ?>
                <div class="alert alert-info">You haven't posted any jobs yet.</div>
                <div class="text-center">
                    <a href="./post_job.php" class="btn btn-primary">Post Your First Job</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>