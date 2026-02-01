<?php
    session_start();
    
    if(isset($_COOKIE['status'])){
        header('location: ../Controllers/home.php');
        exit();
    }
    
    $error = "";
    if(isset($_SESSION['error'])){
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
    <script src="../Assets/validate_signup.js"></script>
    <style>
        .signup-container {
            max-width: 550px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
        
        .signup-card {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .signup-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .signup-header p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-group button {
            flex: 1;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body id="top">
    <header>
        <div class="text-center">
            <h1>🏥 MedVerify</h1>
            <p>Medicine Authentication & Verification System</p>
        </div>
    </header>

    <main>
        <div class="signup-container fade-in">
            <div class="signup-card">
                <div class="signup-header">
                    <h2>Create Account</h2>
                    <p>Join MedVerify to verify authentic medicines</p>
                </div>

                <?php if($error != ""){ ?>
                <div class="alert alert-error">
                    ✕ <?php echo $error; ?>
                </div>
                <?php } ?>

                <form action="../Controllers/signupCheck.php" method="post" onsubmit="return validateSignupForm()">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Choose a unique username" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Create password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" name="submit" class="btn btn-success">
                            Create Account
                        </button>
                        <button type="reset" class="btn btn-danger">
                            Clear Form
                        </button>
                    </div>

                    <div class="login-link">
                        <p>Already have an account? <a href="login.php">Sign in here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 MedVerify | All Rights Reserved | <a href="#top">Back to Top</a></p>
    </footer>
</body>
</html>
