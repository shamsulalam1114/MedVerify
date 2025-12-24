<?php
    session_start();

    if(isset($_SESSION['status']) !== true){
        header('location: login.html');
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
        <h1>Welcome Home! <?php echo $_SESSION['username'];?></h1>
        <br>
        <nav>
            <ul>
                <li><a href="../Views/dashboard.php">Dashboard</a></li>
                <li><a href="../Views/view_reports.php">View Reports</a></li>
                <li><a href="../Views/family_profile.php">Family Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
</body>
</html>