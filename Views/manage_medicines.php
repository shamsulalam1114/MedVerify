<?php include '../Controllers/manage_medicines_session.php'; ?>
<?php
    require_once('../Models/medicineModel.php');
    require_once('../Models/manufacturerModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
        header('location: ../Views/verify_medicine.php');
        exit();
    }
    
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'All';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $records_per_page = 20;
    $offset = ($page - 1) * $records_per_page;
    
    if($search_query != ''){
        $all_medicines = searchMedicineByName($search_query);
    }else{
        $all_medicines = getAllMedicines();
    }
    
    $filtered_medicines = [];
    foreach($all_medicines as $med){
        if($filter_status == 'All' || $med['status'] == $filter_status){
            $filtered_medicines[] = $med;
        }
    }
    
    $total_records = count($filtered_medicines);
    $total_pages = ceil($total_records / $records_per_page);
    $medicines = array_slice($filtered_medicines, $offset, $records_per_page);
    
    $total_medicines = getTotalMedicinesCount();
    
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
    <title>Manage Medicines - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <link rel="stylesheet" href="../Assets/print.css" media="print">
    <script src="../Assets/autocomplete.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Medicine Database Management</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="verify_medicine.php">Verify Medicine</a></li>
                <li><a href="verification_history.php">Verification History</a></li>
                <li><a href="manage_medicines.php"><b>Manage Medicines</b></a></li>
                <li><a href="manage_manufacturers.php">Manage Manufacturers</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
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
                    <h2>💊 Medicine Database Management</h2>
                    <p><i>Admin Panel - Add, Edit, Delete Medicines</i></p>
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
                <td align="center" class="card-blue" width="50%">
                    <h3>💊 Total Medicines</h3>
                    <br>
                    <h1><?php echo $total_medicines; ?></h1>
                    <p>In Database</p>
                    <br>
                </td>
                <td align="center" class="card-green" width="50%">
                    <h3>📋 Current View</h3>
                    <br>
                    <h1><?php echo count($medicines); ?></h1>
                    <p>Showing Results</p>
                    <br>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Search and Actions -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>🔍 Search & Actions</h3>
                </td>
            </tr>
        </table>

        <form action="manage_medicines.php" method="get" enctype="" style="display: inline;">
        <table border="1" width="100%">
            <tr>
                <td width="20%"><b>Search Medicine:</b></td>
                <td width="35%">
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Medicine name, generic name..." style="width: 100%">
                </td>
                <td width="20%"><b>Filter by Status:</b></td>
                <td width="25%">
                    <select name="filter_status" style="width: 100%">
                        <option value="All" <?php echo $filter_status == 'All' ? 'selected' : ''; ?>>All Status</option>
                        <option value="Active" <?php echo $filter_status == 'Active' ? 'selected' : ''; ?>>✅ Active</option>
                        <option value="Discontinued" <?php echo $filter_status == 'Discontinued' ? 'selected' : ''; ?>>⚠️ Discontinued</option>
                        <option value="Recalled" <?php echo $filter_status == 'Recalled' ? 'selected' : ''; ?>>❌ Recalled</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center">
                    <input type="submit" value="🔍 Apply Filter" style="padding: 10px 30px;">
                    <a href="manage_medicines.php"><button type="button">🔄 Reset</button></a>
                </td>
            </tr>
        </table>
        </form>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <a href="add_medicine.php"><button style="background-color: lightgreen; padding: 15px 30px; font-weight: bold;">➕ Add New Medicine</button></a>
                    <a href="manage_medicines.php"><button>🔄 Reset Search</button></a>
                </td>
            </tr>
        </table>

        <br><br>

        <!-- Medicines Table -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📋 All Medicines (<?php echo $total_records; ?> results - Page <?php echo $page; ?> of <?php echo max(1, $total_pages); ?>)</h3>
                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <td align="center">
                    <a href="../Controllers/export_medicines_csv.php?search=<?php echo urlencode($search_query); ?>">
                        <button type="button" style="background-color: #4CAF50; color: white; padding: 10px 20px; font-weight: bold;">📥 Export to CSV</button>
                    </a>
                    <button type="button" onclick="window.print()" style="background-color: #2196F3; color: white; padding: 10px 20px; font-weight: bold;">🖨️ Print Database</button>
                </td>
            </tr>
        </table>

        <br>

        <table border="1" width="100%" style="font-size: 13px;">
            <tr>
                <th>ID</th>
                <th>Medicine Name</th>
                <th>Generic Name</th>
                <th>Manufacturer</th>
                <th>Category</th>
                <th>Form</th>
                <th>Strength</th>
                <th>Barcode</th>
                <th>Batch</th>
                <th>Expiry Date</th>
                <th>MRP</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            if(count($medicines) > 0){
                foreach($medicines as $medicine){
                    $expiry_status = checkMedicineExpiry($medicine['expiry_date']);
                    $expiry_color = 'black';
                    if($expiry_status == 'Expired') $expiry_color = 'red';
                    else if($expiry_status == 'Near Expiry') $expiry_color = 'orange';
                    
                    $status_color = 'green';
                    if($medicine['status'] == 'Recalled') $status_color = 'red';
                    else if($medicine['status'] == 'Discontinued') $status_color = 'orange';
            ?>
            <tr>
                <td align="center"><?php echo $medicine['medicine_id']; ?></td>
                <td><b><?php echo $medicine['medicine_name']; ?></b></td>
                <td><?php echo $medicine['generic_name']; ?></td>
                <td><?php echo $medicine['manufacturer_name']; ?><br><small><?php echo $medicine['country']; ?></small></td>
                <td><?php echo $medicine['category']; ?></td>
                <td><?php echo $medicine['dosage_form']; ?></td>
                <td><?php echo $medicine['strength']; ?></td>
                <td><?php echo $medicine['barcode']; ?></td>
                <td><?php echo $medicine['batch_number']; ?></td>
                <td style="color: <?php echo $expiry_color; ?>; font-weight: <?php echo ($expiry_status == 'Expired') ? 'bold' : 'normal'; ?>;">
                    <?php echo date('M d, Y', strtotime($medicine['expiry_date'])); ?>
                    <?php if($expiry_status == 'Expired') echo '<br><small>EXPIRED</small>'; ?>
                    <?php if($expiry_status == 'Near Expiry') echo '<br><small>Expiring Soon</small>'; ?>
                </td>
                <td align="right">₹<?php echo number_format($medicine['mrp'], 2); ?></td>
                <td align="center" style="color: <?php echo $status_color; ?>; font-weight: bold;">
                    <?php echo $medicine['status']; ?>
                </td>
                <td align="center">
                    <a href="edit_medicine.php?id=<?php echo $medicine['medicine_id']; ?>"><button style="font-size: 11px; background-color: lightblue;">Edit</button></a>
                    <a href="../Controllers/delete_medicine.php?id=<?php echo $medicine['medicine_id']; ?>" onclick="return confirm('Delete this medicine from database?')"><button style="font-size: 11px; background-color: lightcoral;">Delete</button></a>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="13" align="center" style="padding: 30px;">
                    <h3>No medicines found</h3>
                    <p>Try adjusting your search query or <a href="add_medicine.php">add a new medicine</a></p>
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
                    $query_params = "search=" . urlencode($search_query) . "&filter_status=" . urlencode($filter_status);
                    
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

        <!-- Legend -->
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>📖 Status Legend</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%">
            <tr>
                <td width="25%" align="center" style="background-color: #e6ffe6; padding: 10px;">
                    <b style="color: green;">Active</b><br>
                    <small>Available for verification</small>
                </td>
                <td width="25%" align="center" style="background-color: #fff3e6; padding: 10px;">
                    <b style="color: orange;">Discontinued</b><br>
                    <small>No longer manufactured</small>
                </td>
                <td width="25%" align="center" style="background-color: #ffe6e6; padding: 10px;">
                    <b style="color: red;">Recalled</b><br>
                    <small>Withdrawn from market</small>
                </td>
                <td width="25%" align="center" style="background-color: #fff3e6; padding: 10px;">
                    <b style="color: orange;">Near Expiry</b><br>
                    <small>Expires within 90 days</small>
                </td>
            </tr>
        </table>

        <br>

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
