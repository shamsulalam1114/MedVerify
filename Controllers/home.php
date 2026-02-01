<?php
    session_start();

    if(!isset($_COOKIE['status'])){
        header('location: ../Views/login.php');
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Welcome Home</b></p>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <li><a href="../Views/dashboard.php">Dashboard</a></li>
                <li><a href="../Views/verification_history.php">Verification History</a></li>
                <li><a href="../Views/view_reports.php">View Reports</a></li>
                <?php } ?>
                <li><a href="../Views/verify_medicine.php">Verify Medicine</a></li>
                <li><a href="../Views/upload_report.php">Upload Report</a></li>
                <li><a href="../Views/calendar.php">Calendar</a></li>
                <li><a href="../Views/family_profile.php">Family Profile</a></li>
                <li><a href="../Views/logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Welcome, <?php echo $_SESSION['full_name']; ?>!</h2>
                    <p><i>Logged in as: <?php echo $_SESSION['username']; ?></i></p>
                </td>
            </tr>
        </table>

        <br><br>

        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Quick Access</h3>
                </td>
            </tr>
        </table>

        <br>

        <table border="1" width="100%">
            <tr>
                <td align="center" class="card-green" width="25%">
                    <h3>🔍 Verify Medicine</h3>
                    <br>
                    <p>Check medicine authenticity</p>
                    <br>
                    <a href="../Views/verify_medicine.php">Verify Now</a>
                    <br><br>
                </td>
                <td align="center" class="card-blue" width="25%">
                    <h3>My Calendar</h3>
                    <br>
                    <p>View and manage appointments</p>
                    <br>
                    <a href="../Views/calendar.php">Go to Calendar</a>
                    <br><br>
                </td>
                <td align="center" class="card-orange" width="25%">
                    <h3>Upload Report</h3>
                    <br>
                    <p>Upload medical reports</p>
                    <br>
                    <a href="../Views/upload_report.php">Upload Now</a>
                    <br><br>
                </td>
                <td align="center" class="card-blue" width="25%">
                    <h3>Family Profile</h3>
                    <br>
                    <p>Manage family members</p>
                    <br>
                    <a href="../Views/family_profile.php">View Family</a>
                    <br><br>
                </td>
            </tr>
        </table>

        <br><br>

        <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <h3>Admin Quick Links</h3>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <a href="../Views/dashboard.php"><button>Dashboard</button></a>
                    <a href="../Views/view_reports.php"><button>View All Reports</button></a>
                </td>
            </tr>
        </table>

        <br><br>
        <?php } ?>

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