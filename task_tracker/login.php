<?php

session_start();   
require 'db.php'; 

// If user is already logged in, it will skip the login page entirely
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = ''; // Will hold any error message to show the user

// Only run this block when the form is submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email    = trim(strip_tags($_POST['email']));
    $password = $_POST['password']; 

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);  
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result); 

        // password_verify() compares the typed password with
        // the hashed version stored in the database (from registration)
        if ($user && password_verify($password, $user['password'])) {
            // Login success — store user data in the session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            // Wrong email or password — show generic error
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">

    <style>
        
        .sakura-deco {
            position: fixed;
            font-size: 28px;
            opacity: 0.15;
            pointer-events: none;
            user-select: none;
            animation: float 6s ease-in-out infinite;
        }
        .sakura-deco:nth-child(1) { top: 10%; left: 8%;    animation-delay: 0s;   font-size: 24px; }
        .sakura-deco:nth-child(2) { top: 70%; right: 8%;   animation-delay: 1.5s; font-size: 32px; }
        .sakura-deco:nth-child(3) { top: 40%; left: 3%;    animation-delay: 3s;   font-size: 20px; }
        .sakura-deco:nth-child(4) { top: 20%; right: 5%;   animation-delay: 2s;   font-size: 26px; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50%       { transform: translateY(-18px) rotate(8deg); }
        }

        .auth-box {
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ✿ Icon above the form title */
        .auth-icon {
            font-size: 36px;
            text-align: center;
            margin-bottom: 12px;
        }

        /* Center the title and subtitle */
        .auth-box h1,
        .auth-box .subtitle {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- design in the background -->
    <span class="sakura-deco">🌸</span> <!-- 1 -->
    <span class="sakura-deco">✿</span>  <!-- 2 -->
    <span class="sakura-deco">🌸</span> <!-- 3 -->
    <span class="sakura-deco">✦</span>  <!-- 4 -->   

    <div class="auth-wrapper">
        <div class="auth-box">

            <!-- App logo / name -->
            <div class="auth-logo">
                <span><span class="accent">ToDo</span>Bun</span>
            </div>

            <!-- Decorative icon -->
            <div class="auth-icon">🌸</div>

            <h1>Welcome back</h1>
            <p class="subtitle">Log in to see your tasks.</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <!-- Email Field -->
                <div class="field">
                    <label for="email">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="satorugojo@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >

                </div>

                <!-- Password Field -->
                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Your password"
                        required
                        autocomplete="current-password"
                    >
            
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    Log in ✦
                </button>
            </form>
            <!-- Register Button -->
            <p class="divider">
                No account yet? <a href="register.php">Register</a>
            </p>

        </div>
    </div>

</body>
</html>
