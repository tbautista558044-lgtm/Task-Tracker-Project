<?php
// controllers/TaskController.php — Task Controller

class TaskController {

    private $conn;
    private $taskModel;

    public function __construct($conn) {
        $this->conn      = $conn;
        $this->taskModel = new Task($conn);
    }

    // ── dashboard ────────────────────────────────────────────
    public function dashboard() {
        $this->requireLogin();
        $user_id   = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $tasks     = $this->taskModel->getAllByUser($user_id);
        $total     = count($tasks);
        $complete  = count(array_filter($tasks, fn($t) => $t['status'] === 'complete'));
        $pending   = $total - $complete;
        $progress  = $total > 0 ? round(($complete / $total) * 100) : 0;
        $today     = date('Y-m-d');
        $dueToday  = count(array_filter($tasks, fn($t) =>
            $t['status'] === 'pending' &&
            (($t['start_date'] ?? '') <= $today) &&
            (($t['end_date'] ?? $t['due_date'] ?? '') >= $today)
        ));
        $hour      = (int) date('G');
        if ($hour < 12)     $greeting = "Good morning";
        elseif ($hour < 18) $greeting = "Good afternoon";
        else                $greeting = "Good evening";
        $filter = $_GET['filter'] ?? 'all';
        $this->view('dashboard/index', compact(
            'tasks','total','complete','pending','progress','greeting','filter','user_name','dueToday','today'
        ));
    }

    // ── calendar ─────────────────────────────────────────────
    public function calendar() {
        $this->requireLogin();
        $user_id  = $_SESSION['user_id'];
        $tasks    = $this->taskModel->getAllByUser($user_id);
        $total    = count($tasks);
        $complete = count(array_filter($tasks, fn($t) => $t['status'] === 'complete'));
        $pending  = $total - $complete;
        $this->view('calendar/index', compact('tasks','total','complete','pending'));
    }

    // ── add task from calendar (with due_date) ───────────────
    public function addFromCalendar() {
        $this->requireLogin();
        $user_id     = $_SESSION['user_id'];
        $title       = trim(strip_tags($_POST['title']       ?? ''));
        $description = trim(strip_tags($_POST['description'] ?? ''));
        $today       = date('Y-m-d');

        // Use the visible date picker values if set, else fall back to hidden fields
        $start_date = $_POST['start_date_pick'] ?? $_POST['start_date'] ?? $today;
        $end_date   = $_POST['end_date_pick']   ?? $_POST['end_date']   ?? $start_date;

        // Validate format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) $start_date = $today;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date))   $end_date   = $start_date;

        // end_date must not be before start_date
        if ($end_date < $start_date) $end_date = $start_date;

        // Block past dates
        if ($start_date < $today) {
            header("Location: index.php?page=calendar&error=past");
            exit;
        }

        if (!empty($title)) {
            $priority = in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium';
            $category = trim(strip_tags($_POST['category'] ?? '')) ?: null;
            $icon     = trim($_POST['icon'] ?? '') ?: null;
            $this->taskModel->createWithDates($user_id, $title, $description, $start_date, $end_date, $priority, $category, $icon);
        }

        header("Location: index.php?page=calendar&added=1");
        exit;
    }

    // ── addForm ──────────────────────────────────────────────
    public function addForm() {
        $this->requireLogin();
        $this->view('tasks/add', ['error' => '']);
    }

    // ── add (from dashboard form) ────────────────────────────
    public function add() {
        $this->requireLogin();
        $user_id     = $_SESSION['user_id'];
        $title       = trim(strip_tags($_POST['title']));
        $description = trim(strip_tags($_POST['description'] ?? ''));
        $start_date  = $_POST['start_date'] ?? '';
        $end_date    = $_POST['end_date']   ?? '';
        $priority    = in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium';
        $category    = trim(strip_tags($_POST['category'] ?? '')) ?: null;
        $icon        = trim($_POST['icon'] ?? '') ?: null;
        $error       = '';

        if (empty($title)) {
            $error = "Task title is required.";
            $this->view('tasks/add', ['error' => $error]);
            return;
        }

        // Sanitise dates — empty string means no date
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) $start_date = null;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date))   $end_date   = null;
        if ($start_date && $end_date && $end_date < $start_date) $end_date = $start_date;

        if ($start_date) {
            $this->taskModel->createWithDates($user_id, $title, $description, $start_date, $end_date ?? $start_date, $priority, $category, $icon);
        } else {
            $this->taskModel->create($user_id, $title, $description, null, $priority, $category, $icon);
        }
        header("Location: index.php?page=dashboard");
        exit;
    }

    // ── editForm ─────────────────────────────────────────────
    public function editForm() {
        $this->requireLogin();
        $user_id = $_SESSION['user_id'];
        $task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $task    = $this->taskModel->getById($task_id, $user_id);
        if (!$task) { header("Location: index.php?page=dashboard"); exit; }
        $this->view('tasks/edit', ['task' => $task, 'error' => '']);
    }

    // ── edit ─────────────────────────────────────────────────
    public function edit() {
        $this->requireLogin();
        $user_id     = $_SESSION['user_id'];
        $task_id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $title       = trim(strip_tags($_POST['title']));
        $description = trim(strip_tags($_POST['description'] ?? ''));
        $start_date  = $_POST['start_date'] ?? '';
        $end_date    = $_POST['end_date']   ?? '';
        $priority    = in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium';
        $category    = trim(strip_tags($_POST['category'] ?? '')) ?: null;
        $icon        = trim($_POST['icon'] ?? '') ?: null;
        $task        = $this->taskModel->getById($task_id, $user_id);
        if (!$task) { header("Location: index.php?page=dashboard"); exit; }

        if (empty($title)) {
            $task['title'] = $_POST['title'];
            $task['description'] = $_POST['description'] ?? '';
            $this->view('tasks/edit', ['task' => $task, 'error' => "Task title is required."]);
            return;
        }

        // Sanitise dates
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) $start_date = null;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date))   $end_date   = null;
        if ($start_date && $end_date && $end_date < $start_date) $end_date = $start_date;

        if ($this->taskModel->update($task_id, $user_id, $title, $description, $start_date, $end_date, $priority, $category, $icon)) {
            $from = $_POST['from'] ?? $_GET['from'] ?? '';
            header("Location: index.php?page=" . ($from === 'calendar' ? 'calendar' : 'dashboard'));
        } else {
            $this->view('tasks/edit', ['task' => $task, 'error' => "Could not update the task."]);
        }
        exit;
    }

    // ── mark complete / pending ──────────────────────────────
    public function markComplete() {
        $this->requireLogin();
        $task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $this->taskModel->updateStatus($task_id, $_SESSION['user_id'], 'complete');
        $this->redirectBack();
    }

    public function markPending() {
        $this->requireLogin();
        $task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $this->taskModel->updateStatus($task_id, $_SESSION['user_id'], 'pending');
        $this->redirectBack();
    }

    // ── delete ───────────────────────────────────────────────
    public function delete() {
        $this->requireLogin();
        $task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $this->taskModel->delete($task_id, $_SESSION['user_id']);
        $from = $_GET['from'] ?? '';
        header("Location: index.php?page=" . ($from === 'calendar' ? 'calendar' : 'dashboard'));
        exit;
    }

    // ── private helpers ──────────────────────────────────────
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login"); exit;
        }
    }

    private function redirectBack() {
        $ref = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=dashboard';
        header("Location: " . $ref); exit;
    }

    private function view($name, $data = []) {
        extract($data);
        require ROOT . "/views/{$name}.php";
    }
}
?>