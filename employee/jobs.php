<?php
session_start();
require('../db.php');
include("../header.php");

// Check if user is logged in as employee
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: ../login.php");
    exit;
}

$employee_id = $_SESSION['user_id'];

// Handle filters
$where = [];
$params = [];

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where[] = "category = '$category'";
}

if (isset($_GET['location']) && !empty($_GET['location'])) {
    $location = mysqli_real_escape_string($conn, $_GET['location']);
    $where[] = "location LIKE '%$location%'";
}

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = mysqli_real_escape_string($conn, $_GET['type']);
    $where[] = "type = '$type'";
}

// Build query
$sql = "SELECT j.*, ep.company_name 
        FROM jobs j
        JOIN employer_profiles ep ON j.employer_id = ep.user_id
        WHERE j.is_active = 1 AND (j.deadline IS NULL OR j.deadline >= CURDATE())";

if (!empty($where)) {
    $sql .= " AND " . implode(" AND ", $where);
}
$sql .= " ORDER BY j.posted_at DESC";

$result = mysqli_query($conn, $sql);
?>


    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="Web Development" <?= isset($_GET['category']) && $_GET['category'] == 'Web Development' ? 'selected' : '' ?>>Web Development</option>
                        <option value="Marketing" <?= isset($_GET['category']) && $_GET['category'] == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                        <option value="Design" <?= isset($_GET['category']) && $_GET['category'] == 'Design' ? 'selected' : '' ?>>Design</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           value="<?= isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '' ?>" 
                           placeholder="City or Remote">
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Job Type</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Full-time" <?= isset($_GET['type']) && $_GET['type'] == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                        <option value="Part-time" <?= isset($_GET['type']) && $_GET['type'] == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                        <option value="Internship" <?= isset($_GET['type']) && $_GET['type'] == 'Internship' ? 'selected' : '' ?>>Internship</option>
                        <option value="Contract" <?= isset($_GET['type']) && $_GET['type'] == 'Contract' ? 'selected' : '' ?>>Contract</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filter Jobs</button>
                    <a href="jobs.php" class="btn btn-outline-secondary">Reset Filters</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Job Listings -->
    <div class="row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($job = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($job['title']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?= htmlspecialchars($job['company_name']) ?> • <?= htmlspecialchars($job['location']) ?>
                            </h6>
                            <p class="card-text">
                                <strong>Description:</strong> <?= htmlspecialchars(substr($job['description'], 0, 150)) ?>...<br>
                                <strong>Requirements:</strong> <?= htmlspecialchars(substr($job['requirements'], 0, 100)) ?>...<br>
                                <strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?><br>
                                <strong>Type:</strong> <?= ucfirst(htmlspecialchars($job['type'])) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Posted: <?= date('M d, Y', strtotime($job['posted_at'])) ?></small>
                                <a href="view_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-primary">Apply Now</a>
                                 <!-- <a href="view_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary">View</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No jobs found matching your criteria. Try adjusting your filters.</div>
            </div>
        <?php endif; ?>
    </div>


<?php
mysqli_close($conn);
include("../footer.php");
?>