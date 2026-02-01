<?php require_once '../Controllers/analytics_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            text-align: center;
        }
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            opacity: 0.9;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        canvas {
            max-height: 400px;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">MedVerify - AI-Powered Medicine Authentication</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="verify_medicine.php">Verify Medicine</a></li>
            <li><a href="verification_history.php">Verification History</a></li>
            <li><a href="manage_medicines.php">Manage Medicines</a></li>
            <li><a href="manage_manufacturers.php">Manage Manufacturers</a></li>
            <li><a href="analytics.php" class="active">Analytics</a></li>
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
            <h1>📊 Analytics Dashboard</h1>
            <p>Comprehensive insights into medicine verification trends and patterns</p>
        </div>

        <!-- Summary Stats -->
        <div class="stats-summary">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h3>Total Verifications (Last 7 Days)</h3>
                <p class="number"><?php echo array_sum(array_column($dailyStats, 'count')); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>Genuine Rate</h3>
                <p class="number"><?php 
                    $total = array_sum(array_column($dailyStats, 'count'));
                    $genuine = array_sum(array_column($dailyStats, 'genuine'));
                    echo $total > 0 ? round(($genuine / $total) * 100, 1) . '%' : '0%';
                ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>Active Medicines</h3>
                <p class="number"><?php echo count($topMedicines); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3>Active Manufacturers</h3>
                <p class="number"><?php echo count($manufacturerStats); ?></p>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="analytics-grid">
            <!-- Verification Trends (Line Chart) -->
            <div class="chart-container">
                <div class="chart-title">📈 Monthly Verification Trends</div>
                <canvas id="verificationTrendsChart"></canvas>
            </div>

            <!-- Category Distribution (Pie Chart) -->
            <div class="chart-container">
                <div class="chart-title">🎯 Category Distribution</div>
                <canvas id="categoryChart"></canvas>
            </div>

            <!-- Top Medicines (Bar Chart) -->
            <div class="chart-container">
                <div class="chart-title">🏆 Top 10 Verified Medicines</div>
                <canvas id="topMedicinesChart"></canvas>
            </div>

            <!-- Geographic Distribution (Bar Chart) -->
            <div class="chart-container">
                <div class="chart-title">🌍 Verifications by Country</div>
                <canvas id="geographicChart"></canvas>
            </div>

            <!-- Manufacturer Counterfeit Rates (Bar Chart) -->
            <div class="chart-container">
                <div class="chart-title">⚠️ Manufacturer Counterfeit Detection Rates</div>
                <canvas id="manufacturerChart"></canvas>
            </div>

            <!-- Last 7 Days Activity (Line Chart) -->
            <div class="chart-container">
                <div class="chart-title">📅 Last 7 Days Activity</div>
                <canvas id="dailyStatsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Chart.js Configuration
        const chartColors = {
            blue: 'rgb(54, 162, 235)',
            green: 'rgb(75, 192, 192)',
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        // 1. Verification Trends (Line Chart)
        const trendsData = <?php echo json_encode($verificationTrends); ?>;
        new Chart(document.getElementById('verificationTrendsChart'), {
            type: 'line',
            data: {
                labels: trendsData.map(d => d.month),
                datasets: [{
                    label: 'Total Verifications',
                    data: trendsData.map(d => d.count),
                    borderColor: chartColors.blue,
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Genuine',
                    data: trendsData.map(d => d.genuine),
                    borderColor: chartColors.green,
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Counterfeit',
                    data: trendsData.map(d => d.counterfeit),
                    borderColor: chartColors.red,
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. Category Distribution (Pie Chart)
        const categoryData = <?php echo json_encode($categoryDistribution); ?>;
        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: categoryData.map(d => d.category),
                datasets: [{
                    data: categoryData.map(d => d.count),
                    backgroundColor: [
                        chartColors.blue,
                        chartColors.green,
                        chartColors.red,
                        chartColors.orange,
                        chartColors.yellow,
                        chartColors.purple,
                        chartColors.grey
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right'
                    }
                }
            }
        });

        // 3. Top Medicines (Horizontal Bar Chart)
        const topMedicinesData = <?php echo json_encode($topMedicines); ?>;
        new Chart(document.getElementById('topMedicinesChart'), {
            type: 'bar',
            data: {
                labels: topMedicinesData.map(d => d.medicine_name),
                datasets: [{
                    label: 'Verification Count',
                    data: topMedicinesData.map(d => d.count),
                    backgroundColor: chartColors.purple
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 4. Geographic Distribution (Bar Chart)
        const geoData = <?php echo json_encode($geographicDistribution); ?>;
        new Chart(document.getElementById('geographicChart'), {
            type: 'bar',
            data: {
                labels: geoData.map(d => d.country),
                datasets: [{
                    label: 'Verifications',
                    data: geoData.map(d => d.count),
                    backgroundColor: chartColors.green
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 5. Manufacturer Counterfeit Rates (Bar Chart)
        const manufacturerData = <?php echo json_encode($manufacturerStats); ?>;
        new Chart(document.getElementById('manufacturerChart'), {
            type: 'bar',
            data: {
                labels: manufacturerData.map(d => d.manufacturer_name),
                datasets: [{
                    label: 'Counterfeit Rate (%)',
                    data: manufacturerData.map(d => d.counterfeit_rate),
                    backgroundColor: manufacturerData.map(d => 
                        d.counterfeit_rate > 20 ? chartColors.red : 
                        d.counterfeit_rate > 10 ? chartColors.orange : 
                        chartColors.green
                    )
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // 6. Last 7 Days Activity (Line Chart)
        const dailyData = <?php echo json_encode($dailyStats); ?>;
        new Chart(document.getElementById('dailyStatsChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [{
                    label: 'Total',
                    data: dailyData.map(d => d.count),
                    borderColor: chartColors.blue,
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Genuine',
                    data: dailyData.map(d => d.genuine),
                    borderColor: chartColors.green,
                    tension: 0.4
                }, {
                    label: 'Suspicious',
                    data: dailyData.map(d => d.suspicious),
                    borderColor: chartColors.orange,
                    tension: 0.4
                }, {
                    label: 'Counterfeit',
                    data: dailyData.map(d => d.counterfeit),
                    borderColor: chartColors.red,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
