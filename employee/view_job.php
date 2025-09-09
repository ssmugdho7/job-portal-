<?php
session_start();
require_once '../db.php';
include '../header.php';

// Check if user is logged in as employee
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employee') {
    header("Location: ../index.php");
    exit;
}

// Check if job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: jobs.php");
    exit;
}

$job_id = (int)$_GET['id'];
$employee_id = $_SESSION['user_id'];

// Get job details
$job_query = "SELECT j.*, ep.company_name, ep.company_description 
              FROM jobs j 
              JOIN employer_profiles ep ON j.employer_id = ep.user_id 
              WHERE j.id = $job_id";
$job_result = mysqli_query($conn, $job_query);
$job = mysqli_fetch_assoc($job_result);

if (!$job) {
    header("Location: jobs.php");
    exit;
}

// Check if already applied
$applied_check = "SELECT id FROM applications 
                 WHERE employee_id = $employee_id AND job_id = $job_id";
$applied_result = mysqli_query($conn, $applied_check);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cover_letter = mysqli_real_escape_string($conn, $_POST['cover_letter'] ?? '');
    $error = '';
    
    // File upload handling
    if (isset($_FILES['cv_upload']) && $_FILES['cv_upload']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/cvs/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Validate file type (PDF or DOC)
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_type = $_FILES['cv_upload']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $error = "Only PDF and Word documents are allowed.";
        } else {
            // Generate unique filename
            $file_ext = pathinfo($_FILES['cv_upload']['name'], PATHINFO_EXTENSION);
            $filename = 'cv_' . $employee_id . '_' . time() . '.' . $file_ext;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['cv_upload']['tmp_name'], $target_path)) {
                // Update employee's CV path in database
                $update_cv = "UPDATE employee_profiles SET cv_path = '$target_path' WHERE user_id = $employee_id";
                if (!mysqli_query($conn, $update_cv)) {
                    $error = "Error saving CV information.";
                }
                
                // Process application
                $insert_query = "INSERT INTO applications 
                                (job_id, employee_id, cover_letter, cv_path, applied_at) 
                                VALUES 
                                ($job_id, $employee_id, '$cover_letter', '$target_path', NOW())";
                
                if (mysqli_query($conn, $insert_query)) {
                    header("Location: dashboard.php?applied=1");
                    exit;
                } else {
                    $error = "Error submitting application: " . mysqli_error($conn);
                }
            } else {
                $error = "Error uploading file.";
            }
        }
    } else {
        // Check if CV already exists
        $cv_check = "SELECT cv_path FROM employee_profiles WHERE user_id = $employee_id";
        $cv_result = mysqli_query($conn, $cv_check);
        $cv_data = mysqli_fetch_assoc($cv_result);
        
        if (empty($cv_data['cv_path'])) {
            $error = "Please upload your CV or use your previously uploaded CV";
        } else {
            // Use existing CV
            $insert_query = "INSERT INTO applications 
                            (job_id, employee_id, cover_letter, cv_path, applied_at) 
                            VALUES 
                            ($job_id, $employee_id, '$cover_letter', '{$cv_data['cv_path']}', NOW())";
            
            if (mysqli_query($conn, $insert_query)) {
                header("Location: dashboard.php?applied=1");
                exit;
            } else {
                echo "<script>
                alert('Something went wrong');
                window.location.href = 'view_job.php';
                </script>";
                
            }
        }
    }
}

// Get employee's CV status
$cv_status_query = "SELECT cv_path FROM employee_profiles WHERE user_id = $employee_id";
$cv_status_result = mysqli_query($conn, $cv_status_query);
$cv_status = mysqli_fetch_assoc($cv_status_result);
?>


    <div class="row">
        <div class="col-md-8">
            <!-- Job Details Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($job['title']) ?></h2>
                    <h4 class="card-subtitle mb-3 text-muted">
                        <?= htmlspecialchars($job['company_name']) ?>
                    </h4>
                    
                    <!-- Job details display remains the same -->

                    <div class="mb-4">
                        <h5>Job Description</h5>
                        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Requirements</h5>
                        <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                            <p><strong>Type:</strong> <?= ucfirst(htmlspecialchars($job['type'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                            <p><strong>Posted:</strong> <?= date('M d, Y', strtotime($job['posted_at'])) ?></p>
                        </div>
                    </div>
                    
                



                    
                    <?php if (mysqli_num_rows($applied_result) > 0): ?>
                        <div class="alert alert-success">
                            You've already applied for this position.
                        </div>
                    <?php else: ?>
                        <!-- Updated Application Form with File Upload -->
                    <form method="post" enctype="multipart/form-data" id="applicationForm">
                        <div class="mb-3">
                            <h5>Apply for this Position</h5>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="cv_upload" class="form-label">Upload Your CV*</label>
                            <input type="file" class="form-control" id="cv_upload" name="cv_upload" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">PDF or Word documents only (Max 2MB)</div>
                        </div>
                        
                        <?php if (!empty($cv_status['cv_path'])): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="use_existing_cv" name="use_existing_cv">
                                <label class="form-check-label" for="use_existing_cv">
                                    Use my existing CV (<a href="<?= htmlspecialchars($cv_status['cv_path']) ?>" target="_blank">view</a>)
                                </label>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                Submit Application
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
      <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Application Status</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($applied_result) > 0): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i> Already Applied
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill"></i> Not Applied Yet
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <a href="jobs.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Jobs
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Your CV Status</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($cv_status['cv_path'])): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-file-earmark-check-fill"></i> CV Ready
                        </div>
                        <a href="<?= htmlspecialchars($cv_status['cv_path']) ?>" 
                           target="_blank" class="btn btn-outline-success w-100 mb-2">
                            View Your CV
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i> No CV Found
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <a href="my_cv.html" class="btn btn-primary">
                            <?= empty($cv_status['cv_path']) ? 'Build Your CV' : 'Update CV' ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php include '../footer.php'; ?>