<?php
// ============================================================
// public/database.config.php — Database Connection
//
// WHAT THIS FILE DOES:
// Reads credentials from the .env file and opens a connection
// to MySQL. Every model file uses:
//   require_once ROOT . '/public/database.config.php';
// to get access to $conn.
//
// WHY .env INSTEAD OF HARDCODING?
// Hardcoding passwords in PHP files means they get uploaded
// to GitHub. .env files are blocked by .gitignore so your
// real credentials never leave your computer.
// ============================================================

// ROOT is defined in index.php as the project's base folder.
// __DIR__ here would point to /public/, not the root — so we
// use the ROOT constant instead for reliability.

//define('DB_HOST', $env['DB_HOST']);
//define('DB_PORT', (int)$env['DB_PORT']);
//define('DB_USER', $env['DB_USER']);
//define('DB_PASS', $env['DB_PASS']);
//define('DB_NAME', $env['DB_NAME']);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // MySQL password
define('DB_NAME', 'task_tracker1');

// Open the MySQL connection
// The 5th argument is the port — required for Railway (not 3306)
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("
        <div style='font-family:sans-serif;padding:40px;color:#7f1d1d;background:#fff1f2;border-left:4px solid #fca5a5;margin:40px;border-radius:12px;'>
            <h2>❌ Database connection failed</h2>
            <p>" . mysqli_connect_error() . "</p>
            <p>Check your <code>.env</code> credentials.</p>
        </div>
    ");
}

// Ensure emoji and special characters work correctly
mysqli_set_charset($conn, 'utf8mb4');
?>
