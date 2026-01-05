<?php include '../Controllers/family_profile_session.php'; ?>
<?php
    require_once('../Models/familyModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    if(!isset($_REQUEST['id'])){
        header('location: ../Views/family_profile.php');
        exit();
    }
    
    $id = $_REQUEST['id'];
    $member = getFamilyMemberById($id);
    
    if(!$member){
        header('location: ../Views/family_profile.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Family Member</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
</head>
<body>
    <header>
        <center>
            <h1>MedVerify</h1>
        </center>
    </header>

    <hr>

    <main>
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Edit Family Member</h2>
                </td>
            </tr>
        </table>

        <br>

        <form action="../Controllers/edit_family_member.php" method="post" enctype="">
        <table border="1" width="100%">
            <tr>
                <td width="30%">Name:</td>
                <td width="70%"><input type="text" name="name" value="<?php echo $member['name']; ?>" required></td>
            </tr>
            <tr>
                <td>Relationship:</td>
                <td><input type="text" name="relationship" value="<?php echo $member['relationship']; ?>" required></td>
            </tr>
            <tr>
                <td>Age:</td>
                <td><input type="number" name="age" value="<?php echo $member['age']; ?>" required></td>
            </tr>
            <tr>
                <td>Blood Group:</td>
                <td><input type="text" name="blood_group" value="<?php echo $member['blood_group']; ?>"></td>
            </tr>
        </table>

        <br>

        <table width="100%">
            <tr>
                <td align="center">
                    <input type="hidden" name="member_id" value="<?php echo $member['member_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $member['user_id']; ?>">
                    <input type="submit" name="submit" value="Update Member">
                    <a href="family_profile.php"><button type="button">Cancel</button></a>
                </td>
            </tr>
        </table>
        </form>
    </main>

    <hr>

    <footer>
        <center>
            <p>&copy; 2025 MedVerify | All Rights Reserved</p>
        </center>
    </footer>
</body>
</html>
