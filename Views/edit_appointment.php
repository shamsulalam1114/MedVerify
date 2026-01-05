<?php include '../Controllers/calendar_session.php'; ?>
<?php
    require_once('../Models/appointmentModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_REQUEST['id'])){
        header('location: ../Views/calendar.php');
        exit();
    }
    
    $id = $_REQUEST['id'];
    $appointment = getAppointmentById($id);
    
    if(!$appointment){
        header('location: ../Views/calendar.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
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
                    <h2>Edit Appointment</h2>
                </td>
            </tr>
        </table>

        <br>

        <form action="../Controllers/edit_appointment.php" method="post" enctype="">
        <table border="1" width="100%">
            <tr>
                <td width="30%">Date:</td>
                <td width="70%"><input type="date" name="appointment_date" value="<?php echo $appointment['appointment_date']; ?>" required></td>
            </tr>
            <tr>
                <td>Time:</td>
                <td><input type="time" name="appointment_time" value="<?php echo $appointment['appointment_time']; ?>"></td>
            </tr>
            <tr>
                <td>Doctor/Lab:</td>
                <td><input type="text" name="doctor_lab" value="<?php echo $appointment['doctor_lab']; ?>" required></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><input type="text" name="appointment_type" value="<?php echo $appointment['appointment_type']; ?>" required></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <select name="status">
                        <option value="Scheduled" <?php if($appointment['status']=='Scheduled') echo 'selected'; ?>>Scheduled</option>
                        <option value="Completed" <?php if($appointment['status']=='Completed') echo 'selected'; ?>>Completed</option>
                        <option value="Cancelled" <?php if($appointment['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $appointment['user_id']; ?>">
                    <input type="submit" name="submit" value="Update Appointment">
                    <a href="calendar.php"><button type="button">Cancel</button></a>
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
