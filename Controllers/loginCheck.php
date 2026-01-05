<?php
    session_start();
    require_once('../Models/userModel.php');
    
    if(isset($_POST['submit'])){
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        if($username == "" || $password == ""){
            echo "null value!";
        }else{

            $user = ['username'=> $username, 'password'=> $password];
            $result = login($user);
            
            if($result){
                
                setcookie('status', 'true', time()+3000, '/');
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['full_name'] = $result['full_name'];
                $_SESSION['user_type'] = $result['user_type'];

                if($result['user_type'] == 'admin'){
                    header('location: home.php');
                }else{
                    header('location: ../Views/calendar.php');
                }
            }else{
                echo "invalid user!";
            }
        }
    }else{
        header('location: ../Views/login.php');
    }
?>