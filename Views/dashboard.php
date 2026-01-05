<?php include '../Controllers/dashboard_session.php'; ?>
<?php
    require_once('../Models/verificationModel.php');
    require_once('../Models/reportModel.php');
    require_once('../Models/appointmentModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    $verificationsCount = getVerificationCount($user_id);
    $reportsCount = getReportCount($user_id);
    $upcomingAppointment = getUpcomingAppointment($user_id);
    $recentActivities = getRecentActivity($user_id);
    
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
                <td align="center" class="card-blue" id="card1">
                    <h3>Total Verifications</h3>
                    <br>
                    <h1 id="verificationsCount"><?php echo $verificationsCount; ?></h1>
                    <p>Checks Completed</p>
                    <br>
                    <a href="view_reports.php">View Details</a>
                </td>

                <td align="center" class="card-green" id="card2">
                    <h3>Upcoming Appointments</h3>
                    <br>
                    <h1 id="appointmentsCount"><?php echo $appointmentDate; ?></h1>
                    <p>Next Appointment</p>
                    <br>
                    <a href="calendar.php">View Calendar</a>
                </td>

                <td  align="center"class="card-orange" id="card3">
                    <h3>Total Reports</h3>
                    <br>
                    <h1 id="reportsCount"><?php echo $reportsCount; ?></h1>
                    <p>Reports Available</p>
                    <br>
                    <a href="view_reports.php">View Reports</a>
                </td>
            </tr>
        </table>

        <br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <button type="button" id="addVerificationBtn">Add Verification</button>
                    <button type="button" id="addReportBtn">Add Report</button>
                    <button type="button" id="resetBtn">Reset Counts</button>
                </td>
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
