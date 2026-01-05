<?php include '../Controllers/view_reports_session.php'; ?>
<?php
    require_once('../Models/reportModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $reports = getReports($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/view_reports.js"></script>
</head>
<body id="top">
    <form action="../Controllers/home.php" method="post" enctype="">
    <header>
        <center>
            <h1>MedVerify</h1>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_reports.php"><b>View Reports</b></a></li>
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
                    <h2>View Medical Reports</h2>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Your Medical Reports</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="reportsTable">
            <tr>
                <th>Upload Date</th>
                <th>Report Name</th>
                <th>Report Type</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
            <?php
            if(count($reports) > 0){
                foreach($reports as $report){
            ?>
            <tr>
                <td><?php echo $report['upload_date']; ?></td>
                <td><?php echo $report['report_name']; ?></td>
                <td><?php echo $report['report_type']; ?></td>
                <td><?php echo $report['notes'] ? $report['notes'] : 'N/A'; ?></td>
                <td><button type="button" onclick="alert('Downloading Report')">Download</button></td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="5" align="center">No reports found</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br><br>

        
        <form action="../Controllers/add_report.php" method="post" enctype="multipart/form-data">
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Add New Report</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%">Report Name:</td>
                <td width="70%"><input type="text" name="report_name" placeholder="Enter report name" required></td>
            </tr>
            <tr>
                <td>Report Type:</td>
                <td><input type="text" name="report_type" placeholder="Blood Test, X-Ray, MRI, etc." required></td>
            </tr>
            <tr>
                <td>Upload File:</td>
                <td><input type="file" name="myfile" required></td>
            </tr>
            <tr>
                <td>Notes:</td>
                <td><input type="text" name="notes" placeholder="Additional notes (optional)" ></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="Add Report">
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
    </form>
</body>
</html>
