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
    
    $appointmentDate = "No Appointment";
    if($upcomingAppointment != false){
        $appointmentDate = $upcomingAppointment['appointment_date'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
</head>
<body id="top">
    <form action="../Controllers/home.php" method="post" enctype="">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Dashboard</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="verify_medicine.php">Verify Medicine</a></li>
                <li><a href="verification_history.php">Verification History</a></li>
                <li><a href="manage_medicines.php">Manage Medicines</a></li>
                <li><a href="manage_manufacturers.php">Manage Manufacturers</a></li>
                <li><a href="analytics.php">Analytics</a></li>
                <li><a href="review_counterfeits.php">Review Reports</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="family_profile.php">Family Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Welcome <?php echo $_SESSION['full_name']; ?></h2>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table border="1" width="100%">
            <tr>
                <td align="center" class="card-blue" id="card1" width="25%">
                    <h3>🔍 Today's Verifications</h3>
                    <br>
                    <h1 id="verificationsCount"><?php echo $todayCount; ?></h1>
                    <p>Medicines Scanned Today</p>
                    <br>
                    <a href="verify_medicine.php">Verify Medicine</a>
                </td>

                <td align="center" class="card-green" id="card2" width="25%">
                    <h3>✅ Genuine Medicines</h3>
                    <br>
                    <h1 id="genuineCount"><?php echo $genuineCount; ?></h1>
                    <p><?php echo $genuinePercentage; ?>% Verified Authentic</p>
                    <br>
                    <a href="verification_history.php">View History</a>
                </td>

                <td align="center" class="card-orange" id="card3" width="25%">
                    <h3>⚠️ Alerts</h3>
                    <br>
                    <h1 id="alertsCount" style="color: <?php echo $counterfeitTotal > 0 ? 'red' : 'green'; ?>;"><?php echo $counterfeitTotal; ?></h1>
                    <p>Counterfeit/Suspicious Detected</p>
                    <br>
                    <a href="verification_history.php">View Alerts</a>
                </td>

                <td align="center" class="card-red" id="card4" width="25%">
                    <h3>🚨 Counterfeit Reports</h3>
                    <br>
                    <h1 style="color: <?php echo $pendingCounterfeits > 0 ? 'red' : 'green'; ?>;"><?php echo $pendingCounterfeits; ?></h1>
                    <p>Pending Review</p>
                    <br>
                    <a href="review_counterfeits.php">Review Reports</a>
                </td>
            </tr>
        </table>

        <br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <a href="verify_medicine.php"><button style="background-color: lightgreen; padding: 15px 30px; font-weight: bold;">🔍 Verify Medicine</button></a>
                    <a href="manage_medicines.php"><button style="background-color: lightblue; padding: 15px 30px; font-weight: bold;">💊 Manage Medicines</button></a>                    <a href="manage_manufacturers.php"><button style="background-color: lightyellow; padding: 15px 30px; font-weight: bold;">🏭 Manage Manufacturers</button></a>                    <a href="review_counterfeits.php"><button style="background-color: lightcoral; padding: 15px 30px; font-weight: bold;">🚨 Review Reports</button></a>
                    <a href="view_reports.php"><button>📄 View Reports</button></a>
                    <a href="calendar.php"><button>📅 Appointments</button></a>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <a href="../Controllers/export_summary_report.php" target="_blank">
                        <button style="background-color: #FF9800; color: white; padding: 12px 25px; font-weight: bold;">📊 Generate Summary Report (Print/PDF)</button>
                    </a>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Unresolved Alerts Notification -->
        <?php if($unresolvedAlerts > 0){ ?>
        <table width="10🕒 Recent Medicine Verifications</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <th>Date & Time</th>
                <th>User</th>
                <th>Medicine</th>
                <th>Barcode</th>
                <th>Method</th>
                <th>Result</th>
                <th>Confidence</th>
            </tr>
            <?php
            if(count($recentVerifications) > 0){
                foreach($recentVerifications as $verify){
                    $result_color = 'black';
                    $result_bg = 'white';
                    if($verify['verification_result'] == 'Genuine'){
                        $result_color = 'green';
                        $result_bg = '#e6ffe6';
                    }else if($verify['verification_result'] == 'Counterfeit'){
                        $result_color = 'red';
                        $result_bg = '#ffe6e6';
                    }else if($verify['verification_result'] == 'Suspicious'){
                        $result_color = 'orange';
                        $result_bg = '#fff3e6';
                    }
            ?>
            <tr style="background-color: <?php echo $result_bg; ?>;">
                <td><?php echo date('M d, H:i', strtotime($verify['verified_at'])); ?></td>
                <td><?php echo $verify['username']; ?></td>
                <td><?php echo $verify['medicine_name'] ? $verify['medicine_name'] : 'Unknown'; ?></td>
                <td><?php echo $verify['barcode_scanned'] ? $verify['barcode_scanned'] : 'N/A'; ?></td>
                <td><?php echo $verify['verification_method']; ?></td>
                <td style="color: <?php echo $result_color; ?>; font-weight: bold;">
                    <?php echo $verify['verification_result']; ?>
                </td>
                <td align="center"><?php echo $verify['confidence_score']; ?>%</td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="7" align="center">No verifications yet</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br><br>

        <!-- Overall Verification Statistics -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📊 Overall Verification Statistics</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <th>Total Verifications</th>
                <th style="color: green;">Genuine</th>
                <th style="color: red;">Counterfeit</th>
                <th style="color: orange;">Suspicious</th>
                <th>Expired</th>
                <th>Not Found</th>
            </tr>
            <tr>
                <td align="center"><b><?php echo $totalVerifications; ?></b></td>
                <td align="center" style="color: green;"><b><?php echo $genuineCount; ?></b></td>
                <td align="center" style="color: red;"><b><?php echo $counterfeitCount; ?></b></td>
                <td align="center" style="color: orange;"><b><?php echo $suspiciousCount; ?></b></td>
                <td align="center"><b><?php echo $verificationStats['expired_count']; ?></b></td>
                <td align="center"><b><?php echo $verificationStats['not_found_count']; ?></b></td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Recent Activity</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Status</th>
            </tr>
            <?php
            if(count($recentActivities) > 0){
                foreach($recentActivities as $activity){
            ?>
            <tr>
                <td><?php echo $activity['created_at']; ?></td>
                <td><?php echo $activity['activity_description']; ?></td>
                <td>Completed</td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="3" align="center">No recent activity</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br><br>

        
        <!-- <table width="100%">
            <tr>
                <td align="center">
                    <h3>Quick Actions</h3>
                    <p>
                        <a href="#">Upload New Report</a> | 
                        <a href="#">Book Appointment</a> | 
                        <a href="#">Contact Support</a>
                    </p>
                </td>
            </tr>
        </table> -->

        <br>

        
        <table border="0" width="100%" cellpadding="10px" cellspacing="0px">
            <tr>
                <td align="center">
                    <a href="#top">Back to Top</a>
                </td>
            </tr>
        </table>
    </main>

    <hr>

    <footer>
        <center>
            <!-- <table>
                <tr>
                    <td>
                        <p>&copy; 2025 MedVerify | All Rights Reserved</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>
                            <a href="#">Privacy Policy</a> | 
                            <a href="#">Terms of Service</a> | 
                            <a href="#">Help</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><a href="#top">Back to Top</a></p>
                    </td>
                </tr>

            </table> -->
            <p>&copy; 2025 MedVerify | All Rights Reserved</p>


        </center>
    </footer>
    </form>
</body>
</html>
