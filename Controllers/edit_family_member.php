<?php
    session_start();
    require_once('../Models/familyModel.php');
    
    if(isset($_POST['submit'])){
        $member_id = $_REQUEST['member_id'];
        $user_id = $_REQUEST['user_id'];
        $name = $_REQUEST['name'];
        $relationship = $_REQUEST['relationship'];
        $age = $_REQUEST['age'];
        $blood_group = $_REQUEST['blood_group'];
        
        if($name == "" || $relationship == "" || $age == ""){
            echo "Name, relationship and age are required!";
        }else{
            
            $member = [
                'member_id'=> $member_id,
                'user_id'=> $user_id,
                'name'=> $name,
                'relationship'=> $relationship,
                'age'=> $age,
                'blood_group'=> $blood_group
            ];
            
            $result = updateFamilyMember($member);
            
            if($result){
                header('location: ../Views/family_profile.php');
            }else{
                echo "Failed to update family member!";
            }
        }
    }else{
        header('location: ../Views/family_profile.php');
    }
?>
