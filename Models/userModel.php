<?php
require_once('db.php');


function login($user){
    $con = getConnection();
    $sql = "select * from users where username='{$user['username']}' and password='{$user['password']}'";
    $result = mysqli_query($con, $sql);   
    
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

function getUserById($id){
    $con = getConnection();
    $sql = "select * from users where user_id='$id'";
    $result = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row;
    }else{
        return false;
    }
}

function getAllUsers(){
    $con = getConnection();
    $sql = "select * from users";
    $result = mysqli_query($con, $sql);
    
    $users = [];
    while($row = mysqli_fetch_assoc($result)){
        array_push($users, $row);
    }
    
    return $users;
}

function addUser($user){
    $con = getConnection();
    $sql = "insert into users (username, password, user_type, full_name) values('{$user['username']}', '{$user['password']}', '{$user['user_type']}', '{$user['full_name']}')";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function deleteUser($id){
    $con = getConnection();
    $sql = "delete from users where user_id='$id'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateUser($user){
    $con = getConnection();
    $sql = "update users set username='{$user['username']}', password='{$user['password']}', email='{$user['email']}', full_name='{$user['full_name']}', blood_group='{$user['blood_group']}', age='{$user['age']}' where user_id='{$user['user_id']}'";
    
    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

?>
