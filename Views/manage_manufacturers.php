<?php require_once '../Controllers/manage_manufacturers_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Manufacturers - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <link rel="stylesheet" href="../Assets/print.css">
</head>
<body>
    <nav>
        <div class="nav-brand">MedVerify - AI-Powered Medicine Authentication</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verify_medicine.php">Verify Medicine</a></li>
            <li><a href="verification_history.php">Verification History</a></li>
            <li><a href="manage_medicines.php">Manage Medicines</a></li>
            <li><a href="manage_manufacturers.php" class="active">Manage Manufacturers</a></li>
            <li><a href="review_counterfeits.php">Review Reports</a></li>
            <li><a href="report_counterfeit.php">Report Counterfeit</a></li>
            <li><a href="family_profile.php">Family Profile</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="upload_report.php">Upload Report</a></li>
            <li><a href="view_reports.php">View Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Manufacturer Management</h1>
            <div class="button-group no-print">
                <button onclick="window.location.href='add_manufacturer.php'" class="btn-primary">+ Add Manufacturer</button>
                <button onclick="window.location.href='../Controllers/export_manufacturers_csv.php?search=<?php echo urlencode($searchQuery); ?>&status=<?php echo urlencode($statusFilter); ?>'" class="btn-export">Export CSV</button>
                <button onclick="window.print()" class="btn-print">Print List</button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="dashboard-cards">
            <div class="card green">
                <h3>Total Manufacturers</h3>
                <p class="stat-number"><?php echo $stats['total_manufacturers']; ?></p>
            </div>
            <div class="card blue">
                <h3>Active Manufacturers</h3>
                <p class="stat-number"><?php echo $stats['active_manufacturers']; ?></p>
            </div>
            <div class="card orange">
                <h3>Total Medicines</h3>
                <p class="stat-number"><?php echo $stats['total_medicines']; ?></p>
            </div>
            <div class="card purple">
                <h3>Verified Manufacturers</h3>
                <p class="stat-number"><?php echo $stats['verified_manufacturers']; ?></p>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="search-section no-print">
            <form method="GET" action="manage_manufacturers.php" class="search-form">
                <input type="text" name="search" placeholder="Search by name, country, or license..." value="<?php echo htmlspecialchars($searchQuery); ?>" class="search-input">
                <select name="status" class="filter-select">
                    <option value="All" <?php echo $statusFilter === 'All' ? 'selected' : ''; ?>>All Status</option>
                    <option value="Active" <?php echo $statusFilter === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $statusFilter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <button type="submit" class="btn-search">Search</button>
                <button type="button" onclick="window.location.href='manage_manufacturers.php'" class="btn-reset">Reset</button>
            </form>
        </div>

        <!-- Manufacturers Table -->
        <div class="table-container">
            <h2>Manufacturers List (<?php echo count($manufacturers); ?> records)</h2>
            <?php if (count($manufacturers) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>License Number</th>
                        <th>License Expiry</th>
                        <th>Contact Email</th>
                        <th>Phone</th>
                        <th>Medicines Count</th>
                        <th>Verification Rate</th>
                        <th>Status</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($manufacturers as $manufacturer): ?>
                    <tr>
                        <td><?php echo $manufacturer['manufacturer_id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($manufacturer['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($manufacturer['country']); ?></td>
                        <td><?php echo htmlspecialchars($manufacturer['license_number'] ?? 'N/A'); ?></td>
                        <td>
                            <?php 
                            if ($manufacturer['license_expiry']) {
                                $expiry = new DateTime($manufacturer['license_expiry']);
                                $now = new DateTime();
                                $diff = $now->diff($expiry)->days;
                                
                                if ($expiry < $now) {
                                    echo '<span class="status-badge counterfeit">EXPIRED</span>';
                                } elseif ($diff <= 30) {
                                    echo '<span class="status-badge suspicious">Expiring Soon</span><br>' . $expiry->format('Y-m-d');
                                } else {
                                    echo $expiry->format('Y-m-d');
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($manufacturer['contact_email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($manufacturer['contact_phone'] ?? 'N/A'); ?></td>
                        <td class="text-center"><?php echo $manufacturer['medicines_count']; ?></td>
                        <td>
                            <?php 
                            $verificationRate = $manufacturer['verification_rate'];
                            if ($verificationRate >= 90) {
                                echo '<span class="status-badge genuine">' . $verificationRate . '%</span>';
                            } elseif ($verificationRate >= 70) {
                                echo '<span class="status-badge suspicious">' . $verificationRate . '%</span>';
                            } else {
                                echo '<span class="status-badge counterfeit">' . $verificationRate . '%</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if ($manufacturer['status'] === 'Active') {
                                echo '<span class="status-badge genuine">Active</span>';
                            } else {
                                echo '<span class="status-badge counterfeit">Inactive</span>';
                            }
                            ?>
                        </td>
                        <td class="no-print">
                            <button onclick="window.location.href='edit_manufacturer.php?id=<?php echo $manufacturer['manufacturer_id']; ?>'" class="btn-small btn-edit">Edit</button>
                            <button onclick="deleteManufacturer(<?php echo $manufacturer['manufacturer_id']; ?>, '<?php echo htmlspecialchars($manufacturer['name']); ?>')" class="btn-small btn-delete">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="no-data">No manufacturers found. <a href="add_manufacturer.php">Add your first manufacturer</a></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deleteManufacturer(id, name) {
            if (confirm('Are you sure you want to delete manufacturer "' + name + '"?\n\nThis will affect all associated medicines.')) {
                window.location.href = '../Controllers/delete_manufacturer.php?id=' + id;
            }
        }
    </script>
</body>
</html>
