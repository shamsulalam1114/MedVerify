<?php
    session_start();
    
    // Database connection
    $con = mysqli_connect('127.0.0.1', 'root', '', 'medverify_new');
    
    if(isset($_POST['submit'])){
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        if($username == "" || $password == ""){
            echo "null value!";
        }else{

            // Check user in database
            $sql = "select * from users where username='$username' and password='$password'";
            $result = mysqli_query($con, $sql);
            
            if(mysqli_num_rows($result) > 0){
                
                $row = mysqli_fetch_assoc($result);
                
                setcookie('status', 'true', time()+3000, '/');
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];

                header('location: home.php');
            }else{
                echo "invalid user!";
            }
        }
    }else{
        header('location: ../Views/login.php');
    }
?>