<?php
    session_start();

    if(isset($_POST['submit'])){
        $username = trim($_REQUEST['username']);
        $password = trim($_REQUEST['password']);

        if(empty($username) || empty($password)){
            echo "Username and password are required!";
        }else{

            if($username == $password){
                
                $_SESSION['status'] = true;
                $_SESSION['username'] = $username;

                header('location: home.php');
            }else{
                echo "invalid user!";
            }
        }
    }else{
        
        header('location: ../Views/login.php');
    }
?>