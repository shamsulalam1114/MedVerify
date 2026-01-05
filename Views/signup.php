<?php
    session_start();
    
    if(isset($_COOKIE['status'])){
        header('location: ../Controllers/home.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Signup Page</title>
    <script src="../Assets/validate_signup.js"></script>
</head>
<body>
        <form action="../Controllers/signupCheck.php" method="post" enctype="" onsubmit="return validateSignupForm()">
            <fieldset>
            <legend>Signup Page</legend>
            <table>
                <tr>
                    <td>Full Name</td>
                    <td><input type="text" name="full_name" value=""></td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td><input type="text" name="username" value=""></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" value=""></td>
                </tr>
                <tr>
                    <td>Confirm Password</td>
                    <td><input type="password" name="confirm_password" value=""></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="submit" value="Signup">
                        <input type="reset" value="Reset">
                    </td>
                </tr>
            </table>
            </fieldset>
        </form>
        <br>
        <center>
            Already have an account? <a href="login.php">Login here</a>
        </center>
</body>
</html>
