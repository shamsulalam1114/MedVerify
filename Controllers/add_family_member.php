<?php
    session_start();
    require_once('../Models/familyModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(isset($_POST['submit'])){
        $user_id = $_SESSION['user_id'];
        $name = $_REQUEST['name'];
        $relationship = $_REQUEST['relationship'];
        $age = $_REQUEST['age'];
        $blood_group = $_REQUEST['blood_group'];
        
        if($name == ""){
            echo "Name is required!";
        }else if($relationship == ""){
            echo "Relationship is required!";
        }else if($age == ""){
            echo "Age is required!";
        }else if($age < 0 || $age > 150){
            echo "Age must be between 0 and 150!";
        }else{
            
            $member = [
                'user_id'=> $user_id,
                'name'=> $name,
                'relationship'=> $relationship,
                'age'=> $age,
                'blood_group'=> $blood_group
            ];
            
            $result = addFamilyMember($member);
            
            if($result){
                header('location: ../Views/family_profile.php');
            }else{
                echo "Failed to add family member!";
            }
        }
    }else{
        header('location: ../Views/family_profile.php');
    }
?>
