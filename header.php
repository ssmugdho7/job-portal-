<?php
// session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Job Portal'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">






</head>

<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                

             <li class="nav-item">
                            <a class="navbar-brand" href="/job-portal2/index.php">JobPortal+</a>
             </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_type'] == 'employer'): ?>
                         <!-- <li class="nav-item">
                            <a class="navbar-brand" href="/job-portal2/index.php">JobPortal+</a>
                        </li> -->
                        <!-- Employer-specific menu items -->
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employer/jobs.php">Jobs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employer/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employer/post_job.php">Post Job</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employer/view_jobs.php">View Jobs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employer/applicants.php">View Applicants</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/employer/manage_jobs.php">Manage Jobs</a>
                        </li>
                      -->
                            
                <?php elseif ($_SESSION['user_type'] == 'employee'): ?>
                        <!-- <li class="nav-item">
                            <a class="navbar-brand" href="/job-portal2/index.php">JobPortal+</a>
                        </li> -->
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employee/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employee/build_cv.html">How to build CV</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employee/applications.php">My Applications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/job-portal2/employee/my_cv.html">My CV</a>
                        </li>
                <?php endif; ?>     
            <?php endif; ?>
   
             
             
           

                <!-- <li class="nav-item">
                    <a class="nav-link" href="admin/jobs.php">Jobs</a>
                </li> -->
              

            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['username']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- <li><a class="dropdown-item" href="profile.php">Profile</a></li> -->
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/job-portal2/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">