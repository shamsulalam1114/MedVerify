<?php include '../Controllers/report_counterfeit_session.php'; ?>
<?php
    require_once('../Models/medicineModel.php');
    require_once('../Models/counterfeitModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    // Get user's previous reports
    $user_id = $_SESSION['user_id'];
    $myReports = getUserCounterfeitReports($user_id);
    
    // Success/Error messages
    $success = "";
    if(isset($_SESSION['success'])){
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
    }
    
    $error = "";
    if(isset($_SESSION['error'])){
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Counterfeit Medicine - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/validate_counterfeit_report.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Report Counterfeit Medicine</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="review_counterfeits.php">Review Reports</a></li>
                <?php } ?>
                <li><a href="verify_medicine.php">Verify Medicine</a></li>
                <li><a href="report_counterfeit.php"><b>Report Counterfeit</b></a></li>
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
                    <h2>🚨 Report Counterfeit or Suspicious Medicine</h2>
                    <p><i>Help protect others by reporting fake or suspicious medicines</i></p>
                </td>
            </tr>
        </table>

        <br>

        <!-- Success/Error Messages -->
        <?php if($success != ""){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <p style="color: green; font-weight: bold; background-color: #e6ffe6; padding: 10px;">✅ <?php echo $success; ?></p>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <?php if($error != ""){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <p style="color: red; font-weight: bold; background-color: #ffe6e6; padding: 10px;">❌ <?php echo $error; ?></p>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <!-- Report Form -->
        <form action="../Controllers/submit_counterfeit_report.php" method="post" enctype="multipart/form-data" onsubmit="return validateCounterfeitReport()">
        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 Medicine Information</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Barcode Number:</b> <span style="color: red;">*</span></td>
                <td width="70%"><input type="text" name="barcode" placeholder="Enter barcode from medicine package" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Batch Number:</b> <span style="color: red;">*</span></td>
                <td><input type="text" name="batch_number" placeholder="Enter batch number" required style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🏪 Purchase Information</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Purchase Location:</b> <span style="color: red;">*</span></td>
                <td width="70%"><input type="text" name="purchase_location" placeholder="Store/Pharmacy name and address" required style="width: 100%"></td>
            </tr>
            <tr>
                <td><b>Purchase Date:</b> <span style="color: red;">*</span></td>
                <td><input type="date" name="purchase_date" required style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>⚠️ Issue Details</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>What issue did you notice?</b> <span style="color: red;">*</span></td>
                <td width="70%">
                    <textarea name="reported_issue" rows="5" placeholder="Describe the issue in detail (e.g., wrong packaging, suspicious quality, no effect, side effects, different color/taste, fake hologram, missing seal, etc.)" required style="width: 100%"></textarea>
                </td>
            </tr>
            <tr>
                <td><b>Upload Evidence Photo:</b> <span style="color: red;">*</span></td>
                <td>
                    <input type="file" name="evidence_photo" accept="image/*" required style="width: 100%">
                    <br><small>Upload a clear photo of the medicine package/label (JPG, PNG, max 5MB)</small>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center" style="background-color: #fff3e6; padding: 15px;">
                    <b>⚠️ Important Notice:</b><br>
                    Your report will be reviewed by our admin team. If verified as counterfeit, appropriate authorities will be notified.
                    Please ensure all information provided is accurate.
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="🚨 Submit Report" style="background-color: lightcoral; padding: 15px 30px; font-weight: bold;">
                    <input type="reset" value="🔄 Clear Form">
                    <a href="verify_medicine.php"><button type="button">❌ Cancel</button></a>
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <!-- User's Previous Reports -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📜 My Previous Reports (<?php echo count($myReports); ?>)</h3>
                </td>
            </tr>
        </table>

        <?php if(count($myReports) > 0){ ?>
        <table border="1" width="100%" style="font-size: 13px;">
            <tr>
                <th>Report ID</th>
                <th>Medicine Name</th>
                <th>Barcode</th>
                <th>Batch Number</th>
                <th>Purchase Location</th>
                <th>Reported Date</th>
                <th>Status</th>
                <th>Admin Notes</th>
            </tr>
            <?php foreach($myReports as $report){ 
                $status_color = 'orange';
                if($report['status'] == 'Verified') $status_color = 'red';
                else if($report['status'] == 'Rejected') $status_color = 'gray';
            ?>
            <tr>
                <td align="center"><?php echo $report['report_id']; ?></td>
                <td><b><?php echo $report['medicine_name'] ? $report['medicine_name'] : 'Unknown Medicine'; ?></b></td>
                <td><?php echo $report['barcode_scanned']; ?></td>
                <td><?php echo $report['batch_number']; ?></td>
                <td><?php echo $report['purchase_location']; ?></td>
                <td><?php echo date('M d, Y', strtotime($report['reported_date'])); ?></td>
                <td align="center" style="color: <?php echo $status_color; ?>; font-weight: bold;">
                    <?php echo $report['status']; ?>
                </td>
                <td><?php echo $report['admin_notes'] ? $report['admin_notes'] : '-'; ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php }else{ ?>
        <table width="100%">
            <tr>
                <td align="center" style="padding: 20px;">
                    <p>You haven't submitted any counterfeit reports yet.</p>
                </td>
            </tr>
        </table>
        <?php } ?>

        <br>

        <!-- Status Legend -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📖 Status Legend</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="33%" align="center" style="background-color: #fff3e6; padding: 10px;">
                    <b style="color: orange;">Pending</b><br>
                    <small>Under review by admin</small>
                </td>
                <td width="33%" align="center" style="background-color: #ffe6e6; padding: 10px;">
                    <b style="color: red;">Verified</b><br>
                    <small>Confirmed counterfeit</small>
                </td>
                <td width="34%" align="center" style="background-color: #f0f0f0; padding: 10px;">
                    <b style="color: gray;">Rejected</b><br>
                    <small>Not a counterfeit</small>
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
            <p>&copy; 2025 MedVerify | Report Counterfeit Medicine</p>
        </center>
    </footer>
</body>
</html>
