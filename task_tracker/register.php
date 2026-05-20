<?php

session_start();
require 'db.php';

// If user is already logged in, it will send to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim(strip_tags($_POST['name']));
    $email    = trim(strip_tags($_POST['email']));
    $password = $_POST['password']; 
    $confirm  = $_POST['confirm'];

    // Validating inputs
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";

    } else {
        // Checks if email is already registered ---
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "An account with that email already exists.";

        } else {
            // Hash the password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // This insert the new user into the database 
            $insert = mysqli_prepare($conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($insert, "sss", $name, $email, $hashed);

            if (mysqli_stmt_execute($insert)) {
                $success = "Account created! You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .sakura-deco:nth-child(1) { top: 8%;  left: 6%;  animation-delay: 0s;   font-size: 22px; }
        .sakura-deco:nth-child(2) { top: 75%; right: 7%; animation-delay: 1s;   font-size: 30px; }
        .sakura-deco:nth-child(3) { top: 50%; left: 2%;  animation-delay: 2.5s; font-size: 18px; }
        .sakura-deco:nth-child(4) { top: 15%; right: 4%; animation-delay: 1.8s; font-size: 26px; }
        .sakura-deco:nth-child(5) { top: 88%; left: 15%; animation-delay: 3.5s; font-size: 20px; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50%       { transform: translateY(-18px) rotate(8deg); }
        }

        /* Card fade-in on page load */
        .auth-box {
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-icon {
            font-size: 36px;
            text-align: center;
            margin-bottom: 12px;
        }

        .auth-box h1,
        .auth-box .subtitle {
            text-align: center;
        }

        /*
            Password strength indicator bar
            Shows a colored bar below the password field
            that changes color as the password gets stronger.
            This is purely visual feedback — no JS framework needed.
        */
        .strength-bar {
            height: 4px;
            border-radius: 99px;
            margin-top: 6px;
            background: var(--border);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .strength-fill {
            height: 100%;
            border-radius: 99px;
            width: 0%;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .strength-label {
            font-size: 11px;
            font-weight: 700;
            margin-top: 4px;
            min-height: 16px;
            transition: color 0.3s;
        }

        .match-hint {
            font-size: 11px;
            font-weight: 700;
            margin-top: 4px;
            min-height: 16px;
        }

        .match-hint.ok  { color: var(--success-text); }
        .match-hint.bad { color: var(--danger-text); }

        .success-actions {
            text-align: center;
            margin-top: 16px;
        }
    </style>
</head>
<body>

    <span class="sakura-deco">🌸</span>
    <span class="sakura-deco">✿</span>
    <span class="sakura-deco">✦</span>
    <span class="sakura-deco">🌸</span>
    <span class="sakura-deco">✿</span>

    <div class="auth-wrapper">
        <div class="auth-box">

            <!-- App logo -->
            <div class="auth-logo">
                <span><span class="accent">ToDo</span>Bun</span>
            </div>

            <!-- Page icon -->
            <div class="auth-icon">✨</div>

            <h1>Create account</h1>
            <p class="subtitle">Start tracking your tasks today.</p>

            <!--
                ERROR ALERT
                Shows when any validation check fails.
            -->
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!--
                SUCCESS ALERT
                Shows after the account is created successfully.
                The form is hidden so they can't submit again.
            -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <div class="success-actions">
                    <a href="login.php" class="btn btn-primary">Go to Login ✦</a>
                </div>

            <?php else: ?>
            <!--
                REGISTRATION FORM
                Only shows if registration hasn't succeeded yet.
                After success, we hide it and show the login button.
            -->
            <form method="POST" action="register.php" id="registerForm">

                <!-- Full Name -->
                <div class="field">
                    <label for="name">Full name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Satoru Gojo"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        required
                        autocomplete="name"
                    >
                </div>

                <!-- Email -->
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

                <!-- Password + strength indicator -->
                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Min. 6 characters"
                        required
                        autocomplete="new-password"
                        oninput="checkStrength(this.value)"
                    >
                    <!--
                        oninput="checkStrength()" runs the JS function
                        every time the user types in this field.
                    -->
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-label" id="strengthLabel"></div>
                </div>

                <!-- Confirm Password + match indicator -->
                <div class="field">
                    <label for="confirm">Confirm password</label>
                    <input
                        type="password"
                        id="confirm"
                        name="confirm"
                        placeholder="Repeat your password"
                        required
                        autocomplete="new-password"
                        oninput="checkMatch()"
                    >
                    <div class="match-hint" id="matchHint"></div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary">
                    Create account ✦
                </button>
            </form>
            <?php endif; ?>

            <!-- Link back to login -->
            <?php if (!$success): ?>
            <p class="divider">
                Already have an account? <a href="login.php">Log in</a>
            </p>
            <?php endif; ?>

        </div>
    </div>


    <script>
        /**
         * checkStrength(password)
         * 
         * Runs every time the user types in the password field.
         * Rates the password from 0–4 and shows a colored bar:
         */
        function checkStrength(password) {
            const fill  = document.getElementById('strengthFill');
            const label = document.getElementById('strengthLabel');

            // Start with score 0
            let score = 0;

            if (password.length === 0) {
                fill.style.width      = '0%';
                fill.style.background = 'transparent';
                label.textContent     = '';
                label.style.color     = '';
                return;
            }

            // Score rules — each one adds 1 point:
            if (password.length >= 6)                           score++; // Long enough
            if (password.length >= 10)                          score++; // Even longer
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++; // Mixed case
            if (/[0-9]/.test(password) || /[^a-zA-Z0-9]/.test(password)) score++; // Number or symbol

            // Map score to visual feedback
            const levels = [
                { width: '25%', color: '#fca5a5', text: 'Too weak',  textColor: '#991b1b' },
                { width: '50%', color: '#fde68a', text: 'Weak',      textColor: '#92400e' },
                { width: '75%', color: '#86efac', text: 'Good',      textColor: '#166534' },
                { width: '100%',color: '#34d399', text: 'Strong ✓',  textColor: '#064e3b' },
            ];

            const level = levels[score - 1] || levels[0];
            fill.style.width      = level.width;
            fill.style.background = level.color;
            label.textContent     = level.text;
            label.style.color     = level.textColor;
        }

        /**
         * checkMatch()
         * -------------------------
         * Runs every time the user types in the confirm field.
         * Shows ✓ if passwords match, ✗ if they don't.
         */
        function checkMatch() {
            const password = document.getElementById('password').value;
            const confirm  = document.getElementById('confirm').value;
            const hint     = document.getElementById('matchHint');

            if (confirm.length === 0) {
                hint.textContent  = '';
                hint.className    = 'match-hint';
                return;
            }

            if (password === confirm) {
                hint.textContent  = '✓ Passwords match';
                hint.className    = 'match-hint ok';
            } else {
                hint.textContent  = '✗ Passwords do not match';
                hint.className    = 'match-hint bad';
            }
        }
    </script>

</body>
</html>