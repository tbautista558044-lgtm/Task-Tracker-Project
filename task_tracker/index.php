<?php
// index.php — The Router (Front Controller)

session_start();
date_default_timezone_set('Asia/Manila');
define('ROOT', dirname(__FILE__));

require ROOT . '/public/database.config.php';

foreach (glob(ROOT . '/models/*.php')      as $file) require $file;
foreach (glob(ROOT . '/controllers/*.php') as $file) require $file;

$page = $_GET['page'] ?? 'login';

switch ($page) {

    // ── Account routes ────────────────────────────────────────
    case 'login':
        (new AccountController($conn))->login();
        break;

    case 'register':
        (new AccountController($conn))->register();
        break;

    case 'logout':
        (new AccountController($conn))->logout();
        break;

    case 'profile':
        (new AccountController($conn))->profile();
        break;

    case 'delete-account':
        (new AccountController($conn))->deleteAccount();
        break;

    // ── Task routes ───────────────────────────────────────────
    case 'dashboard':
        (new TaskController($conn))->dashboard();
        break;

    case 'calendar':
        (new TaskController($conn))->calendar();
        break;

    case 'add-task':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new TaskController($conn))->add();
        } else {
            (new TaskController($conn))->addForm();
        }
        break;

    case 'add-task-calendar':
        (new TaskController($conn))->addFromCalendar();
        break;

    case 'edit-task':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new TaskController($conn))->edit();
        } else {
            (new TaskController($conn))->editForm();
        }
        break;

    case 'mark-complete':
        (new TaskController($conn))->markComplete();
        break;

    case 'mark-pending':
        (new TaskController($conn))->markPending();
        break;

    case 'delete-task':
        (new TaskController($conn))->delete();
        break;

    // ── 404 fallback ──────────────────────────────────────────
    default:
        http_response_code(404);
        echo "
        <div style='font-family:sans-serif;padding:60px;text-align:center;color:#6b5b7b;'>
            <div style='font-size:48px;margin-bottom:16px;'>🌸</div>
            <h2 style='font-size:22px;margin-bottom:8px;'>Page not found</h2>
            <p style='color:#a899b8;margin-bottom:24px;'>The page <code>?page={$page}</code> doesn't exist.</p>
            <a href='index.php?page=dashboard'
               style='background:linear-gradient(135deg,#c084fc,#e879f9);color:white;padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:700;'>
               ← Back to Dashboard
            </a>
        </div>";
        break;
}
?>
