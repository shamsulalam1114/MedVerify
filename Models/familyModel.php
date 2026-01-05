<?php
require_once('db.php');


function getFamilyMembers($user_id){
    $con = getConnection();
    $sql = "select * from family_members where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $members = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($members, $row);
    }
    
    return $members;
}

function getFamilyMemberById($id){
    $con = getConnection();
    $sql = "select * from family_members where member_id='$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

function getFamilyMemberCount($user_id){
    $con = getConnection();
    $sql = "select * from family_members where user_id='$user_id'";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}

function addFamilyMember($member){
    $con = getConnection();
    $sql = "insert into family_members values(null, '{$member['user_id']}', '{$member['name']}', '{$member['relationship']}', '{$member['age']}', '{$member['blood_group']}', null)";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function deleteFamilyMember($id){
    $con = getConnection();
    $sql = "delete from family_members where member_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateFamilyMember($member){
    $con = getConnection();
    $sql = "update family_members set name='{$member['name']}', relationship='{$member['relationship']}', age='{$member['age']}', blood_group='{$member['blood_group']}' where member_id='{$member['member_id']}'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function getAllFamilyMembers(){
    $con = getConnection();
    $sql = "select family_members.*, users.username from family_members join users on family_members.user_id = users.user_id";
    $result = mysqli_query($con, $sql);
    
    $members = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($members, $row);
    }
    
    return $members;
}

function getAllFamilyMembersCount(){
    $con = getConnection();
    $sql = "select * from family_members";
    $result = mysqli_query($con, $sql);
    
    $count = mysqli_num_rows($result);
    return $count;
}

?>
