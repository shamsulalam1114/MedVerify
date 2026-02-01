<?php include '../Controllers/review_counterfeit_session.php'; ?>
<?php
    require_once('../Models/counterfeitModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    // Get filter parameter
    $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'All';
    
    // Get all reports
    $allReports = getAllCounterfeitReports();
    
    // Apply filter
    $reports = [];
    foreach($allReports as $report){
        if($filter_status == 'All' || $report['status'] == $filter_status){
            array_push($reports, $report);
        }
    }
    
    // Get statistics
    $stats = getCounterfeitStats();
    
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
    <title>Review Counterfeit Reports - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Review Counterfeit Reports</b></p>
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
                <li><a href="review_counterfeits.php"><b>Review Reports</b></a></li>
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
                    <h2>🚨 Counterfeit Medicine Reports</h2>
                    <p><i>Admin Panel - Review and Verify Reports</i></p>
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

        <!-- Statistics -->
        <table border="1" width="100%">
            <tr>
                <td align="center" class="card-blue" width="25%">
                    <h3>📊 Total Reports</h3>
                    <br>
                    <h1><?php echo $stats['total_reports']; ?></h1>
                    <p>All Time</p>
                    <br>
                </td>
                <td align="center" class="card-orange" width="25%">
                    <h3>⏳ Pending Review</h3>
                    <br>
                    <h1 style="color: orange;"><?php echo $stats['pending_reports']; ?></h1>
                    <p>Awaiting Action</p>
                    <br>
                </td>
                <td align="center" class="card-red" width="25%">
                    <h3>✅ Verified</h3>
                    <br>
                    <h1 style="color: red;"><?php echo $stats['verified_reports']; ?></h1>
                    <p>Confirmed Counterfeit</p>
                    <br>
                </td>
                <td align="center" class="card-green" width="25%">
                    <h3>❌ Rejected</h3>
                    <br>
                    <h1><?php echo $stats['rejected_reports']; ?></h1>
                    <p>Not Counterfeit</p>
                    <br>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Pending Alert -->
        <?php if($stats['pending_reports'] > 0){ ?>
        <table width="100%" style="background-color: #fff3e6; border: 2px solid orange;">
            <tr>
                <td align="center" style="padding: 10px;">
                    <h3 style="color: orange;">⚠️ <?php echo $stats['pending_reports']; ?> Report(s) Pending Review!</h3>
                    <p>Please review and take action on pending counterfeit reports.</p>
                </td>
            </tr>
        </table>
        <br><br>
        <?php } ?>

        <!-- Filter -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔍 Filter Reports</h3>
                </td>
            </tr>
        </table>

        <form action="review_counterfeits.php" method="get">
        <table border="1" width="100%">
            <tr>
                <td width="30%"><b>Filter by Status:</b></td>
                <td width="70%">
                    <select name="filter_status" style="width: 50%" onchange="this.form.submit()">
                        <option value="All" <?php echo ($filter_status == 'All') ? 'selected' : ''; ?>>All Reports</option>
                        <option value="Pending" <?php echo ($filter_status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Verified" <?php echo ($filter_status == 'Verified') ? 'selected' : ''; ?>>Verified</option>
                        <option value="Rejected" <?php echo ($filter_status == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <!-- Reports Table -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 Counterfeit Reports (<?php echo count($reports); ?> results)</h3>
                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <td align="center">
                    <a href="../Controllers/export_counterfeits_csv.php?filter_status=<?php echo $filter_status; ?>">
                        <button type="button" style="background-color: #4CAF50; color: white; padding: 10px 20px; font-weight: bold;">📥 Export to CSV</button>
                    </a>
                    <button type="button" onclick="window.print()" style="background-color: #2196F3; color: white; padding: 10px 20px; font-weight: bold;">🖨️ Print Report</button>
                </td>
            </tr>
        </table>

        <br>

        <?php if(count($reports) > 0){ ?>
        <table border="1" width="100%" style="font-size: 12px;">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Reported By</th>
                <th>Medicine</th>
                <th>Barcode</th>
                <th>Batch</th>
                <th>Purchase Location</th>
                <th>Issue Description</th>
                <th>Evidence</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach($reports as $report){ 
                $status_color = 'orange';
                $status_bg = '#fff3e6';
                if($report['status'] == 'Verified'){
                    $status_color = 'red';
                    $status_bg = '#ffe6e6';
                }else if($report['status'] == 'Rejected'){
                    $status_color = 'gray';
                    $status_bg = '#f0f0f0';
                }
            ?>
            <tr style="background-color: <?php echo $status_bg; ?>;">
                <td align="center"><?php echo $report['report_id']; ?></td>
                <td><?php echo date('M d, Y', strtotime($report['reported_date'])); ?><br><small><?php echo date('h:i A', strtotime($report['reported_date'])); ?></small></td>
                <td><b><?php echo $report['username']; ?></b><br><small><?php echo $report['email']; ?></small></td>
                <td><?php echo $report['medicine_name'] ? '<b>'.$report['medicine_name'].'</b><br><small>'.$report['manufacturer_name'].'</small>' : '<i>Unknown Medicine</i>'; ?></td>
                <td><?php echo $report['barcode_scanned']; ?></td>
                <td><?php echo $report['batch_number']; ?></td>
                <td><?php echo $report['purchase_location']; ?><br><small><?php echo date('M d, Y', strtotime($report['purchase_date'])); ?></small></td>
                <td style="max-width: 200px;"><small><?php echo substr($report['reported_issue'], 0, 100); ?><?php echo strlen($report['reported_issue']) > 100 ? '...' : ''; ?></small></td>
                <td align="center">
                    <?php if($report['evidence_photo']){ ?>
                    <a href="../uploads/counterfeits/<?php echo $report['evidence_photo']; ?>" target="_blank"><button type="button" style="font-size: 11px;">📷 View Photo</button></a>
                    <?php }else{ ?>
                    <small>No photo</small>
                    <?php } ?>
                </td>
                <td align="center" style="color: <?php echo $status_color; ?>; font-weight: bold;">
                    <?php echo $report['status']; ?>
                    <?php if($report['reviewed_date']){ ?>
                    <br><small style="color: black; font-weight: normal;"><?php echo date('M d, Y', strtotime($report['reviewed_date'])); ?></small>
                    <?php } ?>
                </td>
                <td align="center">
                    <?php if($report['status'] == 'Pending'){ ?>
                    <a href="../Controllers/update_counterfeit_status.php?id=<?php echo $report['report_id']; ?>&status=Verified" onclick="return confirm('Mark this report as VERIFIED counterfeit?')">
                        <button style="font-size: 11px; background-color: lightcoral;">✅ Verify</button>
                    </a>
                    <br>
                    <a href="../Controllers/update_counterfeit_status.php?id=<?php echo $report['report_id']; ?>&status=Rejected" onclick="return confirm('Mark this report as REJECTED (not counterfeit)?')">
                        <button style="font-size: 11px; background-color: lightgray;">❌ Reject</button>
                    </a>
                    <?php }else{ ?>
                    <small>Reviewed</small>
                    <?php } ?>
                    <br>
                    <a href="../Controllers/delete_counterfeit_report.php?id=<?php echo $report['report_id']; ?>" onclick="return confirm('Delete this report?')">
                        <button style="font-size: 11px; background-color: lightcoral;">🗑️ Delete</button>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php }else{ ?>
        <table width="100%">
            <tr>
                <td align="center" style="padding: 30px;">
                    <h3>No counterfeit reports found</h3>
                    <p>No reports match the selected filter.</p>
                </td>
            </tr>
        </table>
        <?php } ?>

        <br><br>

        <!-- Status Legend -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📖 Status Guide</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="33%" align="center" style="background-color: #fff3e6; padding: 10px;">
                    <b style="color: orange;">Pending</b><br>
                    <small>Awaiting admin review</small>
                </td>
                <td width="33%" align="center" style="background-color: #ffe6e6; padding: 10px;">
                    <b style="color: red;">Verified</b><br>
                    <small>Confirmed as counterfeit</small>
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
            <p>&copy; 2025 MedVerify | Admin Panel</p>
        </center>
    </footer>
</body>
</html>
