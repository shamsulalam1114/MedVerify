<?php include '../Controllers/dashboard_session.php'; ?>
<?php
    require_once('../Models/verificationModel.php');
    require_once('../Models/reportModel.php');
    require_once('../Models/appointmentModel.php');
    require_once('../Models/medicineVerificationModel.php');
    require_once('../Models/counterfeitModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/calendar.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Get medicine verification statistics
    $verificationStats = getOverallVerificationStats();
    $todayCount = getTodayVerificationCount();
    $recentVerifications = getRecentVerifications(5);
    $unresolvedAlerts = getUnresolvedAlertsCount();
    
    // Get counterfeit statistics
    $counterfeitStats = getCounterfeitStats();
    $pendingCounterfeits = getPendingCounterfeitCount();
    
    // Calculate percentages
    $totalVerifications = $verificationStats['total_verifications'];
    $genuineCount = $verificationStats['genuine_count'];
    $counterfeitCount = $verificationStats['counterfeit_count'];
    $suspiciousCount = $verificationStats['suspicious_count'];
    
    $genuinePercentage = $totalVerifications > 0 ? round(($genuineCount / $totalVerifications) * 100, 1) : 0;
    $counterfeitTotal = $counterfeitCount + $suspiciousCount;
    
    // Get other stats
    $reportsCount = getAllReportsCount();
    $upcomingAppointment = getUpcomingAppointment($user_id);
    
    // Get recent activity (empty for now - can be populated from activity log)
    $recentActivities = [];
    
    $appointmentDate = "No Appointment";
    if($upcomingAppointment != false){
        $appointmentDate = $upcomingAppointment['appointment_date'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
</head>
<body id="top">
    <form action="../Controllers/home.php" method="post" enctype="">
    <header>
        <div class="text-center">
            <h1>🏥 MedVerify</h1>
            <p>Admin Dashboard - Medicine Verification System</p>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verify_medicine.php">Verify Medicine</a></li>
            <li><a href="verification_history.php">History</a></li>
            <li><a href="manage_medicines.php">Medicines</a></li>
            <li><a href="manage_manufacturers.php">Manufacturers</a></li>
            <li><a href="analytics.php">Analytics</a></li>
            <li><a href="review_counterfeits.php">Reports</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="upload_report.php">Upload</a></li>
            <li><a href="family_profile.php">Family</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <h2 class="section-title fade-in">Welcome, <?php echo $_SESSION['full_name']; ?> 👋</h2>

        <!-- Stats Cards Grid -->
        <div class="grid grid-4 fade-in" style="margin-bottom: 2rem;">
            <div class="card card-blue">
                <h3>🔍 Today's Verifications</h3>
                <h1><?php echo $todayCount; ?></h1>
                <p>Medicines Scanned Today</p>
                <a href="verify_medicine.php">Verify Medicine →</a>
            </div>

            <div class="card card-green">
                <h3>✅ Genuine Medicines</h3>
                <h1><?php echo $genuineCount; ?></h1>
                <p><?php echo $genuinePercentage; ?>% Verified Authentic</p>
                <a href="verification_history.php">View History →</a>
            </div>

            <div class="card card-orange">
                <h3>⚠️ Alerts</h3>
                <h1 style="color: <?php echo $counterfeitTotal > 0 ? '#dc2626' : '#10b981'; ?>;"><?php echo $counterfeitTotal; ?></h1>
                <p>Counterfeit/Suspicious Detected</p>
                <a href="verification_history.php">View Alerts →</a>
            </div>

            <div class="card card-red">
                <h3>🚨 Counterfeit Reports</h3>
                <h1><?php echo $pendingCounterfeits; ?></h1>
                <p>Pending Review</p>
                <a href="review_counterfeits.php">Review Reports →</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card fade-in" style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--gray-900); font-size: 1.25rem;">⚡ Quick Actions</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                <a href="verify_medicine.php"><button class="btn btn-success">🔍 Verify Medicine</button></a>
                <a href="manage_medicines.php"><button class="btn">💊 Manage Medicines</button></a>
                <a href="manage_manufacturers.php"><button class="btn btn-info">🏭 Manufacturers</button></a>
                <a href="review_counterfeits.php"><button class="btn btn-danger">🚨 Review Reports</button></a>
                <a href="view_reports.php"><button class="btn">📄 View Reports</button></a>
                <a href="calendar.php"><button class="btn">📅 Appointments</button></a>
                <a href="../Controllers/export_summary_report.php" target="_blank"><button class="btn btn-warning">📊 Generate Report</button></a>
            </div>
        </div>

        <!-- Unresolved Alerts Notification -->
        <?php if($unresolvedAlerts > 0){ ?>
        <div class="alert alert-warning fade-in" style="margin-bottom: 2rem;">
            <strong>⚠️ Alert:</strong> You have <?php echo $unresolvedAlerts; ?> unresolved counterfeit/suspicious medicines! 
            <a href="verification_history.php" style="color: var(--warning-text); text-decoration: underline; font-weight: 700;">Review Now →</a>
        </div>
        <?php } ?>

        <!-- Recent Verifications -->
        <div class="card fade-in">
            <h3 class="section-title" style="border-color: var(--secondary-color);">🕒 Recent Medicine Verifications</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Medicine</th>
                        <th>Barcode</th>
                        <th>Method</th>
                        <th>Result</th>
                        <th>Confidence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(count($recentVerifications) > 0){
                        foreach($recentVerifications as $verify){
                            $resultClass = '';
                            if($verify['verification_result'] == 'Genuine'){
                                $resultClass = 'status-genuine';
                            }else if($verify['verification_result'] == 'Counterfeit'){
                                $resultClass = 'status-counterfeit';
                            }else if($verify['verification_result'] == 'Suspicious'){
                                $resultClass = 'status-suspicious';
                            }
                    ?>
                    <tr>
                        <td><?php echo date('M d, H:i', strtotime($verify['verified_at'])); ?></td>
                        <td><?php echo $verify['username']; ?></td>
                        <td><?php echo $verify['medicine_name'] ? $verify['medicine_name'] : 'Unknown'; ?></td>
                        <td><?php echo $verify['barcode_scanned'] ? $verify['barcode_scanned'] : 'N/A'; ?></td>
                        <td><?php echo $verify['verification_method']; ?></td>
                        <td><span class="<?php echo $resultClass; ?>"><?php echo $verify['verification_result']; ?></span></td>
                        <td style="text-align: center;"><?php echo $verify['confidence_score']; ?>%</td>
                    </tr>
                    <?php
                        }
                    }else{
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--gray-500);">No verifications yet</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Overall Statistics -->
        <div class="card fade-in" style="margin-top: 2rem;">
            <h3 class="section-title" style="border-color: var(--accent-info);">📊 Overall Verification Statistics</h3>
            <table>
                <thead>
                    <tr>
                        <th>Total Verifications</th>
                        <th>Genuine</th>
                        <th>Counterfeit</th>
                        <th>Suspicious</th>
                        <th>Expired</th>
                        <th>Not Found</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; font-weight: 700; font-size: 1.25rem;"><?php echo $totalVerifications; ?></td>
                        <td style="text-align: center;"><span class="status-genuine" style="font-size: 1.25rem;"><?php echo $genuineCount; ?></span></td>
                        <td style="text-align: center;"><span class="status-counterfeit" style="font-size: 1.25rem;"><?php echo $counterfeitCount; ?></span></td>
                        <td style="text-align: center;"><span class="status-suspicious" style="font-size: 1.25rem;"><?php echo $suspiciousCount; ?></span></td>
                        <td style="text-align: center; font-weight: 700; font-size: 1.25rem; color: var(--gray-600);"><?php echo $verificationStats['expired_count']; ?></td>
                        <td style="text-align: center; font-weight: 700; font-size: 1.25rem; color: var(--gray-600);"><?php echo $verificationStats['not_found_count']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="#top" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">↑ Back to Top</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 MedVerify | All Rights Reserved</p>
    </footer>
    </form>
</body>
</html>
