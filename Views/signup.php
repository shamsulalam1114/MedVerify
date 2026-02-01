<?php
    session_start();
    
    if(isset($_COOKIE['status'])){
        header('location: ../Controllers/home.php');
        exit();
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
    <title>Signup - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/validate_signup.js"></script>
    <script src="../Assets/ajax_handler.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Create Your Account</b></p>
        </center>
    </header>

    <hr>

    <main>
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Signup for MedVerify</h2>
                </td>
            </tr>
        </table>

        <br>

        <?php if($error != ""){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

        <form action="../Controllers/signupCheck.php" method="post" enctype="" onsubmit="return validateSignupForm()">
        <table border="1" width="100%">
            <tr>
                <td width="30%">Full Name:</td>
                <td width="70%"><input type="text" name="full_name" value="" placeholder="Enter your full name" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Username:</td>
                <td><input type="text" name="username" value="" placeholder="Choose a username" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" value="" placeholder="Create a password" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Confirm Password:</td>
                <td><input type="password" name="confirm_password" value="" placeholder="Re-enter your password" style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="Signup">
                    <input type="reset" value="Clear Form">
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <table width="100%">
            <tr>
                <td align="center">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
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
