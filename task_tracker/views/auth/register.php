<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<span class="sakura-deco">🌸</span>
<span class="sakura-deco">✿</span>
<span class="sakura-deco">✦</span>
<span class="sakura-deco">🌸</span>
<span class="sakura-deco">✿</span>

<div class="auth-wrapper">
    <div class="auth-box">

        <div class="auth-icon"><img src="public/images/logo-dailybun.png" alt="logo" style="width:100%;max-width:500px;height:auto;object-fit:contain;display:block;margin:0 auto;"></div>
        <h1><span style="color:var(--primary)">Create account</span></h1>
        <p class="subtitle">Start tracking your tasks today.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <div class="success-actions">
                <a href="index.php?page=login" class="btn btn-primary">Go to Login ✦</a>
            </div>
        <?php else: ?>

        <form method="POST" action="index.php?page=register" id="registerForm">

            <div class="field">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name"
                       placeholder="Satoru Gojo"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       required autocomplete="name">
            </div>

            <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email"
                       placeholder="satorugojo@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autocomplete="email">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Min. 6 characters"
                       required autocomplete="new-password"
                       oninput="checkStrength(this.value)">
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <div class="strength-label" id="strengthLabel"></div>
            </div>

            <div class="field">
                <label for="confirm">Confirm password</label>
                <input type="password" id="confirm" name="confirm"
                       placeholder="Repeat your password"
                       required autocomplete="new-password"
                       oninput="checkMatch()">
                <div class="match-hint" id="matchHint"></div>
            </div>

            <button type="submit" class="btn btn-primary">Create account ✦</button>
        </form>

        <?php endif; ?>

        <?php if (empty($success)): ?>
        <p class="divider">
            Already have an account? <a href="index.php?page=login">Log in</a>
        </p>
        <?php endif; ?>

    </div>
</div>

<script>
    function checkStrength(password) {
        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        if (!password.length) { fill.style.width = '0%'; label.textContent = ''; return; }
        let score = 0;
        if (password.length >= 6)  score++;
        if (password.length >= 10) score++;
        if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password) || /[^a-zA-Z0-9]/.test(password)) score++;
        const levels = [
            { width:'25%', color:'#fca5a5', text:'Too weak',  textColor:'#991b1b' },
            { width:'50%', color:'#fde68a', text:'Weak',      textColor:'#92400e' },
            { width:'75%', color:'#86efac', text:'Good',      textColor:'#166534' },
            { width:'100%',color:'#34d399', text:'Strong ✓',  textColor:'#064e3b' },
        ];
        const l = levels[score - 1] || levels[0];
        fill.style.width = l.width; fill.style.background = l.color;
        label.textContent = l.text; label.style.color = l.textColor;
    }

    function checkMatch() {
        const pw   = document.getElementById('password').value;
        const cf   = document.getElementById('confirm').value;
        const hint = document.getElementById('matchHint');
        if (!cf.length) { hint.textContent = ''; hint.className = 'match-hint'; return; }
        if (pw === cf) { hint.textContent = '✓ Passwords match';        hint.className = 'match-hint ok'; }
        else           { hint.textContent = '✗ Passwords do not match'; hint.className = 'match-hint bad'; }
    }
</script>

</body>
</html>