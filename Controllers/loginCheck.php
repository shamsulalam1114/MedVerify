<?php
    session_start();
    //print_r($_GET);
    //var_dump($_GET);
    if(isset($_POST['submit'])){
        $username = trim($_REQUEST['username']);
        $password = trim($_REQUEST['password']);

        if(empty($username) || empty($password)){
            echo "Username and password are required!";
        }else{

            if($username == $password){
                //echo "valid user!";
                $_SESSION['status'] = true;
                $_SESSION['username'] = $username;

                header('location: home.php');
            }else{
                echo "invalid user!";
            }
        }
    }else{
        //echo "please submit login form!";
        header('location: ../Views/login.php');
    }
?>