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
    <title>Today's Schedule</title>
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
                    <h2>Today's Schedule - December 25, 2025</h2>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Today's Appointments</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="todayAppointmentsTable">
            <tr>
                <th>Time</th>
                <th>Doctor/Lab</th>
                <th>Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>10:00 AM</td>
                <td>Dr. Ahmed Khan</td>
                <td>General Checkup</td>
                <td>Confirmed</td>
                <td><button type="button" onclick="alert('Viewing appointment details')">View</button></td>
            </tr>
            <tr>
                <td>02:30 PM</td>
                <td>City Lab</td>
                <td>Blood Test</td>
                <td>Pending</td>
                <td><button type="button" onclick="alert('Viewing appointment details')">View</button></td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Upcoming This Week</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="weekAppointmentsTable">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor/Lab</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Dec 26, 2025</td>
                <td>11:00 AM</td>
                <td>Dr. Sarah Ahmed</td>
                <td>Follow-up</td>
                <td>Confirmed</td>
            </tr>
            <tr>
                <td>Dec 28, 2025</td>
                <td>09:00 AM</td>
                <td>Heart Clinic</td>
                <td>ECG Test</td>
                <td>Scheduled</td>
            </tr>
            <tr>
                <td>Dec 30, 2025</td>
                <td>03:00 PM</td>
                <td>Dr. Rahman</td>
                <td>Consultation</td>
                <td>Confirmed</td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Past Appointments</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="pastAppointmentsTable">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor/Lab</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Dec 20, 2025</td>
                <td>10:30 AM</td>
                <td>Dr. Karim</td>
                <td>Consultation</td>
                <td>Completed</td>
            </tr>
            <tr>
                <td>Dec 15, 2025</td>
                <td>02:00 PM</td>
                <td>ABC Lab</td>
                <td>X-Ray</td>
                <td>Completed</td>
            </tr>
            <tr>
                <td>Dec 10, 2025</td>
                <td>11:00 AM</td>
                <td>Dr. Hasan</td>
                <td>Follow-up</td>
                <td>Completed</td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Schedule New Appointment</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%">Date:</td>
                <td width="70%"><input type="date" id="appointmentDate" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Time:</td>
                <td><input type="time" id="appointmentTime" style="width: 100%"></td>
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
                    <button type="button" id="addAppointmentBtn">Schedule Appointment</button>
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
