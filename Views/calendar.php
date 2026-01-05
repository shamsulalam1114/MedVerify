<?php include '../Controllers/calendar_session.php'; ?>
<?php
    require_once('../Models/appointmentModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
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
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <?php } ?>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="calendar.php">Calendar</a></li>
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
                <th>Action</th>
            </tr>
            <?php
            if(count($appointments) > 0){
                foreach($appointments as $appointment){
            ?>
            <tr>
                <td><?php echo $appointment['appointment_date']; ?></td>
                <td><?php echo $appointment['doctor_lab']; ?></td>
                <td><?php echo $appointment['appointment_type']; ?></td>
                <td>
                    <!-- <a href="edit_appointment.php?id=<?php echo $appointment['appointment_id']; ?>">Edit</a> | -->
                    <a href="../Controllers/delete_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</a>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="4" align="center">No appointments found</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br><br>

        
        <form action="../Controllers/add_appointment.php" method="post" enctype="" onsubmit="return validateAppointmentForm()">
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
                <td width="70%"><input type="date" name="appointment_date" required style="width: 100%"></td>
            </tr>
            <tr>
                <td>Time:</td>
                <td><input type="time" name="appointment_time" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Doctor/Lab:</td>
                <td><input type="text" name="doctor_lab" placeholder="Enter doctor or lab name" required style="width: 100%"></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><input type="text" name="appointment_type" placeholder="e.g., Checkup, Blood Test" required style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="Add Appointment">
                    <input type="reset" value="Clear Form">
                </td>
            </tr>
        </table>
        </form>

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
