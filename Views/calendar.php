<?php include '../Controllers/calendar_session.php'; ?>
<?php
    if(isset($_POST['addAppointment'])){
        $appointmentDate = $_POST['appointmentDate'];
        $appointmentTime = $_POST['appointmentTime'];
        $appointmentDoctor = $_POST['appointmentDoctor'];
        $appointmentType = $_POST['appointmentType'];

        if($appointmentDate == "" || $appointmentTime == "" || $appointmentDoctor == "" || $appointmentType == ""){
            echo "Please fill all fields!";
        }else{
            // Process the appointment addition
            echo "Appointment added successfully!";
        }
    }
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
                <li><a href="upload_report.html">Upload Report</a></li>
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
            <tr>
                <td>Dec 26, 2025</td>
                <td>Dr. Ahmed</td>
                <td>Checkup</td>
            </tr>
            <tr>
                <td>Dec 28, 2025</td>
                <td>City Lab</td>
                <td>Blood Test</td>
            </tr>
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
