<?php include '../Controllers/family_profile_session.php'; ?>
<?php
    require_once('../Models/familyModel.php');
    
    if(!isset($_SESSION['user_id'])){
        header('location: ../Views/login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){
        $members = getAllFamilyMembers();
        $memberCount = getAllFamilyMembersCount();
    }else{
        $members = getFamilyMembers($user_id);
        $memberCount = getFamilyMemberCount($user_id);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Family Profile</title>
    <link rel="stylesheet" href="../Assets/dashboard.css">
    <script src="../Assets/family_profile.js"></script>
    <script src="../Assets/validate_family.js"></script>
</head>
<body id="top">
    <form action="../Controllers/home.php" method="post" enctype="">
    <header>
        <center>
            <h1>MedVerify</h1>

        </center>
    </header>

    <nav>
        <center>
            <ul>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_reports.php">View Reports</a></li>
                <?php } ?>
                <li><a href="upload_report.php">Upload Report</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="family_profile.php"><b>Family Profile</b></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </center>
    </nav>

    <hr>

    <main>
        
        <table width="100%">
            <tr>
                <td align="center">
                    <h2>Family Profile</h2>
                    
                </td>
            </tr>
        </table>

        <br><br>

        
        <table border="1" width="100%">
            <tr>
                <td width="100%" align="center" >
                    <h3>Total Family Members</h3>
                    <br>
                    <h1 id="familyMembersCount"><?php echo $memberCount; ?></h1>
                    <p>Registered Members</p>
                </td>
            </tr>
        </table>

        <br><br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <h3><?php echo (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') ? 'All Users Family Members' : 'Family Members'; ?></h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" id="familyMembersTable">
            <tr>
                <th>Member ID</th>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <th>Username</th>
                <?php } ?>
                <th>Name</th>
                <th>Relationship</th>
                <th>Age</th>
                <th>Blood Group</th>
                <th>Action</th>
            </tr>
            <?php
            if(count($members) > 0){
                foreach($members as $member){
            ?>
            <tr>
                <td><?php echo $member['member_id']; ?></td>
                <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){ ?>
                <td><?php echo $member['username']; ?></td>
                <?php } ?>
                <td><?php echo $member['name']; ?></td>
                <td><?php echo $member['relationship']; ?></td>
                <td><?php echo $member['age']; ?></td>
                <td><?php echo $member['blood_group']; ?></td>
                <td>
                    <!-- <a href="edit_family_member.php?id=<?php echo $member['member_id']; ?>">Edit</a> | -->
                    <a href="../Controllers/delete_family_member.php?id=<?php echo $member['member_id']; ?>" onclick="return confirm('Are you sure you want to delete this member?')">Delete</a>
                </td>
            </tr>
            <?php
                }
            }else{
            ?>
            <tr>
                <td colspan="<?php echo (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') ? '7' : '6'; ?>" align="center">No family members found</td>
            </tr>
            <?php
            }
            ?>
        </table>

        <br><br>

        </form>
        <form action="../Controllers/add_family_member.php" method="post" enctype="" onsubmit="return validateFamilyForm()">
        <table width="100%" >
            <tr>
                <td align="center">
                    <h3>Add New Family Member</h3>
                </td>
            </tr>
        </table>

        <table border="1" width="100%" >
            <tr>
                <td width="30%">Name:</td>
                <td width="70%"><input type="text" name="name" placeholder="Enter name" required style="width: 100%"></td>
            </tr>
            <tr>
                <td>Relationship:</td>
                <td><input type="text" name="relationship" placeholder="e.g., Father, Mother, Child" required style="width: 100%"></td>
            </tr>
            <tr>
                <td>Age:</td>
                <td><input type="number" name="age" placeholder="Enter age" required style="width: 100%"></td>
            </tr>
            <tr>
                <td>Blood Group:</td>
                <td><input type="text" name="blood_group" placeholder="e.g., A+, B-, O+" style="width: 100%"></td>
            </tr>
        </table>

        <br>

        
        <table width="100%">
            <tr>
                <td align="center">
                    <input type="submit" name="submit" value="Add Family Member">
                    <input type="reset" value="Clear Form">
                </td>
            </tr>
        </table>
        </form>

        <br>

        <form action="../Controllers/home.php" method="post" enctype="">
        <table  width="100%">
            <tr>
                <td align="center">
                    <a href="#top">Back to Top</a>
                </td>
            </tr>
        </table>
    </main>

    <hr>

    <footer>
        <center>
            <p>&copy; 2025 MedVerify | All Rights Reserved</p>
        </center>
    </footer>
    </form>
</body>
</html>
