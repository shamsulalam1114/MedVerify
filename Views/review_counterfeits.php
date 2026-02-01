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
    
    $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'All';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $records_per_page = 20;
    $offset = ($page - 1) * $records_per_page;
    
    $allReports = getAllCounterfeitReports();
    
    $filtered_reports = [];
    foreach($allReports as $report){
        $status_match = ($filter_status == 'All' || $report['status'] == $filter_status);
        
        $search_match = true;
        if($search_query != ''){
            $search_match = (
                stripos($report['medicine_name'], $search_query) !== false ||
                stripos($report['suspected_manufacturer'], $search_query) !== false ||
                stripos($report['barcode'], $search_query) !== false ||
                stripos($report['username'], $search_query) !== false
            );
        }
        
        $date_match = true;
        if($date_from != '' || $date_to != ''){
            $report_date = date('Y-m-d', strtotime($report['reported_at']));
            if($date_from != '' && $report_date < $date_from) $date_match = false;
            if($date_to != '' && $report_date > $date_to) $date_match = false;
        }
        
        if($status_match && $search_match && $date_match){
            array_push($filtered_reports, $report);
        }
    }
    
    $total_records = count($filtered_reports);
    $total_pages = ceil($total_records / $records_per_page);
    $reports = array_slice($filtered_reports, $offset, $records_per_page);
    
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
    <link rel="stylesheet" href="../Assets/professional.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
    <script src="../Assets/autocomplete.js"></script>
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
                    <h3>🔍 Search & Filter Reports</h3>
                </td>
            </tr>
        </table>

        <form action="review_counterfeits.php" method="get">
        <table border="1" width="100%">
            <tr>
                <td width="15%"><b>Filter by Status:</b></td>
                <td width="20%">
                    <select name="filter_status" style="width: 100%">
                        <option value="All" <?php echo ($filter_status == 'All') ? 'selected' : ''; ?>>All Reports</option>
                        <option value="Pending" <?php echo ($filter_status == 'Pending') ? 'selected' : ''; ?>>⏳ Pending</option>
                        <option value="Verified Fake" <?php echo ($filter_status == 'Verified Fake') ? 'selected' : ''; ?>>✅ Verified Fake</option>
                        <option value="Genuine" <?php echo ($filter_status == 'Genuine') ? 'selected' : ''; ?>>❌ Genuine</option>
                        <option value="Under Investigation" <?php echo ($filter_status == 'Under Investigation') ? 'selected' : ''; ?>>🔍 Investigating</option>
                    </select>
                </td>
                <td width="15%"><b>Search:</b></td>
                <td width="20%">
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Medicine, Manufacturer..." style="width: 100%">
                </td>
                <td width="15%"><b>Date From:</b></td>
                <td width="15%">
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" style="width: 100%">
                </td>
            </tr>
            <tr>
                <td><b>Date To:</b></td>
                <td>
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" style="width: 100%">
                </td>
                <td colspan="4" align="center">
                    <input type="submit" value="🔍 Apply Filter" style="padding: 10px 30px;">
                    <a href="review_counterfeits.php"><button type="button">🔄 Reset</button></a>
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <!-- Reports Table -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 Counterfeit Reports (<?php echo $total_records; ?> results - Page <?php echo $page; ?> of <?php echo max(1, $total_pages); ?>)</h3>
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

        <br>

        <!-- Pagination -->
        <?php if($total_pages > 1){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <?php
                    $query_params = "filter_status=" . urlencode($filter_status) . "&search=" . urlencode($search_query) . "&date_from=" . urlencode($date_from) . "&date_to=" . urlencode($date_to);
                    
                    if($page > 1){
                        echo '<a href="?page=1&' . $query_params . '"><button>« First</button></a> ';
                        echo '<a href="?page=' . ($page - 1) . '&' . $query_params . '"><button>‹ Prev</button></a> ';
                    }
                    
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for($i = $start_page; $i <= $end_page; $i++){
                        if($i == $page){
                            echo '<button style="background-color: #667eea; color: white; font-weight: bold;">' . $i . '</button> ';
                        }else{
                            echo '<a href="?page=' . $i . '&' . $query_params . '"><button>' . $i . '</button></a> ';
                        }
                    }
                    
                    if($page < $total_pages){
                        echo '<a href="?page=' . ($page + 1) . '&' . $query_params . '"><button>Next ›</button></a> ';
                        echo '<a href="?page=' . $total_pages . '&' . $query_params . '"><button>Last »</button></a>';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <br>

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
    
    <script>
        initAutocomplete('search', '../Controllers/autocomplete_medicines.php');
    </script>
</body>
</html>
