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
    
    $success = "";
    if(isset($_SESSION['success'])){
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedVerify</title>
    <link rel="stylesheet" href="../Assets/professional.css">
    <script src="../Assets/validate_login.js"></script>
    <style>
        .login-container {
            max-width: 480px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
        
        .login-card {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .signup-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }
        
        .signup-link a:hover {
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
        <div class="login-container fade-in">
            <div class="login-card">
                <div class="login-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account to continue</p>
                </div>

                <?php if($success != ""){ ?>
                <div class="alert alert-success">
                    ✓ <?php echo $success; ?>
                </div>
                <?php } ?>

                <?php if($error != ""){ ?>
                <div class="alert alert-error">
                    ✕ <?php echo $error; ?>
                </div>
                <?php } ?>

                <form action="../Controllers/loginCheck.php" method="post" onsubmit="return validateLoginForm()">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" name="submit" class="submit-btn">
                        Sign In
                    </button>

                    <div class="signup-link">
                        <p>Don't have an account? <a href="signup.php">Create one here</a></p>
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