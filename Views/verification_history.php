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
    
    // Get filter parameters
    $filter_result = isset($_GET['filter_result']) ? $_GET['filter_result'] : 'All';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Get all verifications
    $verifications = getAllVerifications();
    
    // Apply filters
    $filtered_verifications = [];
    foreach($verifications as $verify){
        // Filter by result
        $result_match = ($filter_result == 'All' || $verify['verification_result'] == $filter_result);
        
        // Filter by search
        $search_match = true;
        if($search_query != ''){
            $search_match = (
                stripos($verify['barcode_scanned'], $search_query) !== false ||
                stripos($verify['batch_number_entered'], $search_query) !== false ||
                stripos($verify['medicine_name'], $search_query) !== false ||
                stripos($verify['username'], $search_query) !== false
            );
        }
        
        if($result_match && $search_match){
            array_push($filtered_verifications, $verify);
        }
    }
    
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
    <link rel="stylesheet" href="../Assets/dashboard.css">
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Verification History & Alerts</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="verify_medicine.php">Verify Medicine</a></li>
                <li><a href="verification_history.php"><b>Verification History</b></a></li>
                <li><a href="manage_medicines.php">Manage Medicines</a></li>
                <li><a href="review_counterfeits.php">Review Reports</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="family_profile.php">Family Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        <!-- Title -->
        <table width="100%">
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
                <td width="20%"><b>Filter by Result:</b></td>
                <td width="30%">
                    <select name="filter_result" style="width: 100%">
                        <option value="All" <?php echo $filter_result == 'All' ? 'selected' : ''; ?>>All Results</option>
                        <option value="Genuine" <?php echo $filter_result == 'Genuine' ? 'selected' : ''; ?>>✅ Genuine Only</option>
                        <option value="Counterfeit" <?php echo $filter_result == 'Counterfeit' ? 'selected' : ''; ?>>❌ Counterfeit Only</option>
                        <option value="Suspicious" <?php echo $filter_result == 'Suspicious' ? 'selected' : ''; ?>>⚠️ Suspicious Only</option>
                        <option value="Expired" <?php echo $filter_result == 'Expired' ? 'selected' : ''; ?>>⏰ Expired Only</option>
                        <option value="Not Found" <?php echo $filter_result == 'Not Found' ? 'selected' : ''; ?>>🔍 Not Found Only</option>
                    </select>
                </td>
                <td width="20%"><b>Search:</b></td>
                <td width="30%">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Barcode, Medicine, User..." style="width: 100%">
                </td>
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
                    <h3>📋 Verification Records (<?php echo count($filtered_verifications); ?> results)</h3>
                </td>
            </tr>
        </table>

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

        <br><br>

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
</body>
</html>
