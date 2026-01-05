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
    <title>Login Page</title>
    <script src="../Assets/validate_login.js"></script>
</head>
<body>
        <form action="../Controllers/loginCheck.php" method="post" enctype="" onsubmit="return validateLoginForm()">
            <fieldset>
            <legend>Login Page</legend>
            <table>
                <tr>
                    <td>Username</td>
                    <td><input type="text" name="username" value=""></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password" value=""></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="Submit"></td>
                </tr>
            </table>
            </fieldset>
        </form>
        <br>
        <center>
            Don't have an account? <a href="signup.php">Signup here</a>
        </center>
</body>
</html>