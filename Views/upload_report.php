<?php include '../Controllers/upload_report_session.php'; ?>
<?php
    if(isset($_POST['uploadReport'])){
        $reportName = $_POST['reportName'];
        $reportDate = $_POST['reportDate'];
        $reportType = $_POST['reportType'];
        $reportFile = $_POST['reportFile'];

        if($reportName == "" || $reportDate == "" || $reportType == "" || $reportFile == ""){
            echo "Please fill all fields!";
        }else{
            // Process the report upload
            echo "Report uploaded successfully!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Report</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/upload_report.js"></script>
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
                <li><a href="upload_report.php"><b>Upload Report</b></a></li>
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
                    <h2>Upload Medical Report</h2>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Report Information</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%">Report Name:</td>
                <td width="70%"><input type="text" id="reportName" placeholder="Enter report name" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Report Date:</td>
                <td><input type="text" id="reportDate" placeholder="dd-mm-yyyy" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Report Type:</td>
                <td><input type="text" id="reportType" placeholder="e.g., Blood Test, X-Ray" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Upload File:</td>
                <td><input type="text" id="reportFile" placeholder="File name" style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <button type="button" id="uploadBtn">Upload Report</button>
                    <button type="button" id="clearBtn">Clear Form</button>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Recently Uploaded Reports</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="uploadedReportsTable">
            <tr>
                <th>Report Name</th>
                <th>Date</th>
                <th>Type</th>
            </tr>
            <tr>
                <td>Blood Test Report</td>
                <td>Dec 20, 2025</td>
                <td>Blood Test</td>
            </tr>
            <tr>
                <td>X-Ray Chest</td>
                <td>Dec 15, 2025</td>
                <td>X-Ray</td>
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
