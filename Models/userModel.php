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
    $sql = "update users set username='{$user['username']}', password='{$user['password']}', full_name='{$user['full_name']}', user_type='{$user['user_type']}' where user_id='{$user['user_id']}'";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateUserProfile($userData){
    $con = getConnection();

    $userId = mysqli_real_escape_string($con, $userData['user_id']);
    $fullName = mysqli_real_escape_string($con, $userData['full_name']);
    $email = mysqli_real_escape_string($con, $userData['email']);
    $gender = mysqli_real_escape_string($con, $userData['gender'] ?? '');
    $dateOfBirth = $userData['date_of_birth'] ? "'{$userData['date_of_birth']}'" : 'NULL';
    $phone = mysqli_real_escape_string($con, $userData['phone'] ?? '');
    $address = mysqli_real_escape_string($con, $userData['address'] ?? '');

    $sql = "UPDATE users 
            SET full_name='$fullName', 
                email='$email', 
                gender='$gender', 
                date_of_birth=$dateOfBirth, 
                phone='$phone', 
                address='$address' 
            WHERE user_id='$userId'";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function updateUserPassword($userId, $newPassword){
    $con = getConnection();
    $userId = mysqli_real_escape_string($con, $userId);
    $hashedPassword = md5($newPassword); // Use password_hash() for production

    $sql = "UPDATE users SET password='$hashedPassword' WHERE user_id='$userId'";

    if(mysqli_query($con, $sql)){
        return true;
    }else{
        return false;
    }
}

function verifyUserPassword($userId, $password){
    $con = getConnection();
    $userId = mysqli_real_escape_string($con, $userId);
    $hashedPassword = md5($password); // Match with current hashing

    $sql = "SELECT user_id FROM users WHERE user_id='$userId' AND password='$hashedPassword'";
    $result = mysqli_query($con, $sql);

    return mysqli_num_rows($result) === 1;
}

function emailExistsExcept($email, $userId){
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);
    $userId = mysqli_real_escape_string($con, $userId);

    $sql = "SELECT COUNT(*) as count FROM users WHERE email='$email' AND user_id != '$userId'";
    $result = mysqli_query($con, $sql);

    if($row = mysqli_fetch_assoc($result)){
        return $row['count'] > 0;
    }
    return false;
}

?>
