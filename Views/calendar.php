<?php include '../Controllers/calendar_session.php'; ?>
<?php
    require_once('../Models/appointmentModel.php');
    
    $user_id = $_SESSION['user_id'];
    $appointments = getAppointments($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/calendar.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
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
                    <h2>My Appointments</h2>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Upcoming Appointments</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="appointmentsTable">
            <tr>
                <th>Date</th>
                <th>Doctor/Lab</th>
                <th>Type</th>
            </tr>
            <?php
            if(count($appointments) > 0){
                foreach($appointments as $appointment){
            ?>
            <tr>
                <td><?php echo $appointment['appointment_date']; ?></td>
                <td><?php echo $appointment['doctor_lab']; ?></td>
                <td><?php echo $appointment['appointment_type']; ?></td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="3" align="center">No appointments found</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Add New Appointment</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%">Date:</td>
                <td width="70%"><input type="text" id="appointmentDate" placeholder="dd-mm-yyyy" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Doctor/Lab:</td>
                <td><input type="text" id="appointmentDoctor" placeholder="Enter doctor or lab name" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><input type="text" id="appointmentType" placeholder="e.g., Checkup, Blood Test" style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <button type="button" id="addAppointmentBtn">Add Appointment</button>
                    <button type="button" id="clearFormBtn">Clear Form</button>
                </td>
            </tr>
        </table>

        <br>

        
        <table width="100%">
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
            <p>&copy; 2025 MedVerify | All Rights Reserved</p>
        </center>
    </footer>
</body>
</html>
