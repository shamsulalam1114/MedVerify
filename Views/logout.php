<?php
    session_start();
    
    
    session_destroy();
    unset($_SESSION['status']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Logout</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/logout.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
        </center>
    </header>

    <nav>
        <center>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="family_profile.php">Family Profile</a></li>
                <li><a href="login.php"><b>Login</b></a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Logged out successfully!</h2>
                    <br>
                    <p><i>Thank you for using MedVerify</i></p>
                </td>
            </tr>
        </table>

        <br><br>

       
        <!-- <table border="1" width="100%">
            <tr>
                <td width="100%" align="center" valign="top" class="card-blue">
                    <h3>Session Ended</h3>
                    <br>
                    <p>Your session has been terminated.</p>
                    <p>All your data is secure.</p>
                    <br>
                    <p id="logoutTime">Logged out at: <span id="timeDisplay">--:--:--</span></p>
                </td>
            </tr>
        </table> -->

        <br><br>

        
        <table border="0" width="100%">
            <tr>
                <td align="center">
                    <h3>What would you like to do?</h3>
                </td>
            </tr>
        </table>

        <br>

        <table border="0" width="100%">
            <tr>
                <td align="center">
                    <button id="loginAgainBtn">Login Again</button>
                    <button id="goHomeBtn">Go to Homepage</button>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table border="0" width="100%">
            <tr>
                <td align="center">
                    <h3>Security Tips</h3>
                    <ul>
                        <li>Always logout</li>
                        <li>Keep your password secure</li>
                        <li>Enable two-factor authentication</li>
                        <li>Check your account activity regularly</li>
                    </ul>
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
            <p>&copy; 2025 MedVerify | All Rights Reserved</p>

        </center>
    </footer>
</body>
</html>
