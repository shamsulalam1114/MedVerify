<?php include '../Controllers/view_reports_session.php'; ?>
<?php
    require_once('../Models/reportModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_REQUEST['id'])){
        header('location: ../Views/view_reports.php');
        exit();
    }
    
    $id = $_REQUEST['id'];
    $report = getReportById($id);
    
    if(!$report){
        header('location: ../Views/view_reports.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Report</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
</head>
<body>
    <header>
        <center>
            <h1>MedVerify</h1>
        </center>
    </header>

    <hr>

    <main>
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Edit Report</h2>
                </td>
            </tr>
        </table>

        <br>

        <form action="../Controllers/edit_report.php" method="post" enctype="">
        <table border="1" width="100%">
            <tr>
                <td width="30%">Report Name:</td>
                <td width="70%"><input type="text" name="report_name" value="<?php echo $report['report_name']; ?>" required></td>
            </tr>
            <tr>
                <td>Report Type:</td>
                <td><input type="text" name="report_type" value="<?php echo $report['report_type']; ?>" required></td>
            </tr>
            <tr>
                <td>File Path:</td>
                <td><input type="text" name="file_path" value="<?php echo $report['file_path']; ?>" required></td>
            </tr>
            <tr>
                <td>Notes:</td>
                <td><input type="text" name="notes" value="<?php echo $report['notes']; ?>"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $report['user_id']; ?>">
                    <input type="hidden" name="upload_date" value="<?php echo $report['upload_date']; ?>">
                    <input type="submit" name="submit" value="Update Report">
                    <a href="view_reports.php"><button type="button">Cancel</button></a>
                </td>
            </tr>
        </table>
        </form>
    </main>

    <hr>

    <footer>
        <center>
            <p>&copy; 2025 MedVerify | All Rights Reserved</p>
        </center>
    </footer>
</body>
</html>
