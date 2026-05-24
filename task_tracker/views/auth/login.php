<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>

<span class="sakura-deco">🌸</span>
<span class="sakura-deco">✿</span>
<span class="sakura-deco">🌸</span>
<span class="sakura-deco">✦</span>

<div class="auth-wrapper">
    <div class="auth-box">

        <div class="auth-logo">
            <span><span class="accent">//</span> task_tracker</span>
        </div>

        <div class="auth-icon">🌸</div>
        <h1>Welcome back</h1>
        <p class="subtitle">Log in to see your tasks.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!--
            ACTION points to index.php?page=login (the router).
            The router reads ?page=login and calls AccountController->login()
            which handles the POST and re-shows this view if there's an error.
        -->
        <form method="POST" action="index.php?page=login">

            <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email"
                       placeholder="juan@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autocomplete="email">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Your password"
                       required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary">Log in ✦</button>
        </form>

        <p class="divider">
            No account yet? <a href="index.php?page=register">Register</a>
        </p>

    </div>
</div>

</body>
</html>
