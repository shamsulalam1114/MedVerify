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
    
    $success = "";
    if(isset($_SESSION['success'])){
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedVerify</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/validate_login.js"></script>
</head>
<body id="top">
    <header>
        <center>
            <h1>MedVerify</h1>
            <p><b>Secure Login System</b></p>
        </center>
    </header>

    <hr>

    <main>
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Login to Your Account</h2>
                </td>
            </tr>
        </table>

        <br>

        <?php if($success != ""){ ?>
        <table width="100%">
            <tr>
                <td align="center">
                    <p style="color: green; font-weight: bold;"><?php echo $success; ?></p>
                </td>
            </tr>
        </table>
        <br>
        <?php } ?>

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

        <form action="../Controllers/loginCheck.php" method="post" enctype="" onsubmit="return validateLoginForm()">
        <table border="1" width="100%">
            <tr>
                <td width="30%">Username:</td>
                <td width="70%"><input type="text" name="username" value="" placeholder="Enter your username" style="width: 100%"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" value="" placeholder="Enter your password" style="width: 100%"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="Login">
                </td>
            </tr>
        </table>
        </form>

        <br><br>

        <table width="100%">
            <tr>
                <td align="center">
                    <p>Don't have an account? <a href="signup.php">Signup here</a></p>
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