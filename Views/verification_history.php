<?php include '../Controllers/verification_history_session.php'; ?>
<?php
    require_once('../Models/medicineVerificationModel.php');
    require_once('../Models/medicineModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    $filter_result = isset($_GET['filter_result']) ? $_GET['filter_result'] : 'All';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $records_per_page = 20;
    $offset = ($page - 1) * $records_per_page;
    
    $verifications = getAllVerifications();
    
    $filtered_verifications = [];
    foreach($verifications as $verify){
        $result_match = ($filter_result == 'All' || $verify['verification_result'] == $filter_result);
        
        $search_match = true;
        if($search_query != ''){
            $search_match = (
                stripos($verify['barcode_scanned'], $search_query) !== false ||
                stripos($verify['batch_number_entered'], $search_query) !== false ||
                stripos($verify['medicine_name'], $search_query) !== false ||
                stripos($verify['username'], $search_query) !== false
            );
        }
        
        $date_match = true;
        if($date_from != '' || $date_to != ''){
            $verify_date = date('Y-m-d', strtotime($verify['verified_at']));
            if($date_from != '' && $verify_date < $date_from) $date_match = false;
            if($date_to != '' && $verify_date > $date_to) $date_match = false;
        }
        
        if($result_match && $search_match && $date_match){
            array_push($filtered_verifications, $verify);
        }
    }
    
    $total_records = count($filtered_verifications);
    $total_pages = ceil($total_records / $records_per_page);
    $filtered_verifications = array_slice($filtered_verifications, $offset, $records_per_page);
    
    // Get statistics
    $stats = getOverallVerificationStats();
    $unresolvedAlerts = getUnresolvedAlertsCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification History - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
    <script src="../Assets/autocomplete.js"></script>
</head>
<body id="top">
    <header>
        <div class="text-center">
            <h1>🏥 MedVerify</h1>
            <p>Verification History & Alerts</p>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verify_medicine.php">Verify Medicine</a></li>
            <li><a href="verification_history.php" style="color: var(--primary-color);">History</a></li>
            <li><a href="manage_medicines.php">Medicines</a></li>
            <li><a href="manage_manufacturers.php">Manufacturers</a></li>
            <li><a href="analytics.php">Analytics</a></li>
            <li><a href="review_counterfeits.php">Reports</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="upload_report.php">Upload</a></li>
            <li><a href="family_profile.php">Family</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <div class="fade-in">
            <tr>
                <td align="center">
                    <h2>📊 Medicine Verification History</h2>
                    <p><i>Admin Panel - All User Verifications</i></p>
                </td>
            </tr>
        </table>

        <br>

        <!-- Statistics Summary -->
        <table border="1" width="100%">
            <tr>
                <td align="center" class="card-blue" width="20%">
                    <h3>Total</h3>
                    <h1><?php echo $stats['total_verifications']; ?></h1>
                    <p>Verifications</p>
                </td>
                <td align="center" class="card-green" width="20%">
                    <h3>✅ Genuine</h3>
                    <h1><?php echo $stats['genuine_count']; ?></h1>
                    <p>Authentic</p>
                </td>
                <td align="center" style="background-color: #ffe6e6; padding: 20px;" width="20%">
                    <h3>❌ Counterfeit</h3>
                    <h1 style="color: red;"><?php echo $stats['counterfeit_count']; ?></h1>
                    <p>Fake Detected</p>
                </td>
                <td align="center" class="card-orange" width="20%">
                    <h3>⚠️ Suspicious</h3>
                    <h1><?php echo $stats['suspicious_count']; ?></h1>
                    <p>Need Review</p>
                </td>
                <td align="center" style="background-color: lightyellow; padding: 20px;" width="20%">
                    <h3>🚨 Alerts</h3>
                    <h1 style="color: <?php echo $unresolvedAlerts > 0 ? 'red' : 'green'; ?>;"><?php echo $unresolvedAlerts; ?></h1>
                    <p>Unresolved</p>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Search and Filter Form -->
        <form action="verification_history.php" method="get" enctype="">
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔍 Search & Filter</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="15%"><b>Filter by Result:</b></td>
                <td width="20%">
                    <select name="filter_result" style="width: 100%">
                        <option value="All" <?php echo $filter_result == 'All' ? 'selected' : ''; ?>>All Results</option>
                        <option value="Genuine" <?php echo $filter_result == 'Genuine' ? 'selected' : ''; ?>>✅ Genuine Only</option>
                        <option value="Counterfeit" <?php echo $filter_result == 'Counterfeit' ? 'selected' : ''; ?>>❌ Counterfeit Only</option>
                        <option value="Suspicious" <?php echo $filter_result == 'Suspicious' ? 'selected' : ''; ?>>⚠️ Suspicious Only</option>
                        <option value="Expired" <?php echo $filter_result == 'Expired' ? 'selected' : ''; ?>>⏰ Expired Only</option>
                        <option value="Not Found" <?php echo $filter_result == 'Not Found' ? 'selected' : ''; ?>>🔍 Not Found Only</option>
                    </select>
                </td>
                <td width="15%"><b>Search:</b></td>
                <td width="20%">
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Barcode, Medicine, User..." style="width: 100%">
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
                <td colspan="4"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" value="🔍 Apply Filter" style="padding: 10px 30px;">
                    <a href="verification_history.php"><button type="button">🔄 Reset</button></a>
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <!-- Verification History Table -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 Verification Records (<?php echo $total_records; ?> results - Page <?php echo $page; ?> of <?php echo max(1, $total_pages); ?>)</h3>
                </td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td align="center">
                    <a href="../Controllers/export_verification_csv.php?filter_result=<?php echo $filter_result; ?>&search=<?php echo urlencode($search_query); ?>">
                        <button type="button" style="background-color: #4CAF50; color: white; padding: 10px 20px; font-weight: bold;">📥 Export to CSV</button>
                    </a>
                    <button type="button" onclick="window.print()" style="background-color: #2196F3; color: white; padding: 10px 20px; font-weight: bold;">🖨️ Print Report</button>
                </td>
            </tr>
        </table>

        <br>
        <table border="1" width="100%" style="font-size: 13px;">
            <tr>
                <th>ID</th>
                <th>Date & Time</th>
                <th>User</th>
                <th>Medicine</th>
                <th>Barcode</th>
                <th>Batch</th>
                <th>Method</th>
                <th>Result</th>
                <th>Confidence</th>
                <th>Expiry</th>
                <th>Action</th>
            </tr>
            <?php
            if(count($filtered_verifications) > 0){
                foreach($filtered_verifications as $verify){
                    $result_color = 'black';
                    $result_bg = 'white';
                    if($verify['verification_result'] == 'Genuine'){
                        $result_color = 'green';
                        $result_bg = '#e6ffe6';
                    }else if($verify['verification_result'] == 'Counterfeit'){
                        $result_color = 'red';
                        $result_bg = '#ffe6e6';
                    }else if($verify['verification_result'] == 'Suspicious'){
                        $result_color = 'orange';
                        $result_bg = '#fff3e6';
                    }else if($verify['verification_result'] == 'Expired'){
                        $result_color = 'purple';
                        $result_bg = '#f3e6ff';
                    }
            ?>
            <tr style="background-color: <?php echo $result_bg; ?>;">
                <td align="center">#<?php echo $verify['verification_id']; ?></td>
                <td><?php echo date('M d, Y H:i', strtotime($verify['verified_at'])); ?></td>
                <td><?php echo $verify['username']; ?><br><small><?php echo $verify['full_name']; ?></small></td>
                <td>
                    <?php if($verify['medicine_name']){ ?>
                        <b><?php echo $verify['medicine_name']; ?></b><br>
                        <small><?php echo $verify['generic_name']; ?></small><br>
                        <small style="color: gray;"><?php echo $verify['manufacturer_name']; ?></small>
                    <?php }else{ ?>
                        <i>Unknown Medicine</i>
                    <?php } ?>
                </td>
                <td><?php echo $verify['barcode_scanned'] ? $verify['barcode_scanned'] : '-'; ?></td>
                <td><?php echo $verify['batch_number_entered'] ? $verify['batch_number_entered'] : '-'; ?></td>
                <td><?php echo $verify['verification_method']; ?></td>
                <td style="color: <?php echo $result_color; ?>; font-weight: bold;">
                    <?php 
                    if($verify['verification_result'] == 'Genuine') echo '✅ ';
                    else if($verify['verification_result'] == 'Counterfeit') echo '❌ ';
                    else if($verify['verification_result'] == 'Suspicious') echo '⚠️ ';
                    else if($verify['verification_result'] == 'Expired') echo '⏰ ';
                    else if($verify['verification_result'] == 'Not Found') echo '🔍 ';
                    echo $verify['verification_result']; 
                    ?>
                </td>
                <td align="center"><?php echo $verify['confidence_score']; ?>%</td>
                <td align="center" style="<?php echo ($verify['expiry_check'] == 'Expired') ? 'color: red; font-weight: bold;' : ''; ?>">
                    <?php echo $verify['expiry_check']; ?>
                </td>
                <td align="center">
                    <a href="verification_result.php?id=<?php echo $verify['verification_id']; ?>"><button style="font-size: 11px;">View</button></a>
                    <a href="../Controllers/delete_verification.php?id=<?php echo $verify['verification_id']; ?>" onclick="return confirm('Delete this verification record?')"><button style="font-size: 11px; background-color: lightcoral;">Delete</button></a>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="11" align="center" style="padding: 30px;">
                    <h3>No verifications found</h3>
                    <p>Try adjusting your filters or search query</p>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br>

        <!-- Pagination -->
        <?php if($total_pages > 1){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <?php
                    $query_params = "filter_result=" . urlencode($filter_result) . "&search=" . urlencode($search_query) . "&date_from=" . urlencode($date_from) . "&date_to=" . urlencode($date_to);
                    
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

        <!-- Export Options -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📥 Export Options</h3>
                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <td align="center">
                    <button onclick="window.print()" style="padding: 10px 20px;">🖨️ Print Report</button>
                    <button style="padding: 10px 20px; background-color: lightgreen;">📊 Export to CSV</button>
                    <button style="padding: 10px 20px; background-color: lightblue;">📄 Export to PDF</button>
                </td>
            </tr>
        </table>

        <br>

        <!-- Alert Information -->
        <?php if($unresolvedAlerts > 0){ ?>
        <table width="100%" style="background-color: #ffe6e6; border: 2px solid red;">
            <tr>
                <td align="center" style="padding: 20px;">
                    <h3 style="color: red;">🚨 <?php echo $unresolvedAlerts; ?> Unresolved Alert<?php echo $unresolvedAlerts > 1 ? 's' : ''; ?></h3>
                    <p><b>Action Required:</b> Review counterfeit and suspicious medicines detected above.</p>
                    <ul style="text-align: left; display: inline-block;">
                        <li>Contact users who scanned fake medicines</li>
                        <li>Alert local health authorities</li>
                        <li>Update medicine database with recall status</li>
                        <li>Investigate counterfeit sources</li>
                    </ul>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <!-- Back to Top -->
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
