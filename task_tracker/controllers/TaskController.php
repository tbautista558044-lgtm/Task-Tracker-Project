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
        $hour      = (int) date('G');
        if ($hour < 12)     $greeting = "Good morning";
        elseif ($hour < 18) $greeting = "Good afternoon";
        else                $greeting = "Good evening";
        $filter = $_GET['filter'] ?? 'all';
        $this->view('dashboard/index', compact(
            'tasks','total','complete','pending','progress','greeting','filter','user_name'
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
        $due_date    = $_POST['due_date'] ?? date('Y-m-d');

        // Basic date validation
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) {
            $due_date = date('Y-m-d');
        }

        if (!empty($title)) {
            $this->taskModel->create($user_id, $title, $description, $due_date);
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
        $error       = '';
        if (empty($title)) {
            $error = "Task title is required.";
            $this->view('tasks/add', ['error' => $error]);
            return;
        }
        if ($this->taskModel->create($user_id, $title, $description)) {
            header("Location: index.php?page=dashboard");
        } else {
            $this->view('tasks/add', ['error' => "Could not save the task. Please try again."]);
        }
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
        $task        = $this->taskModel->getById($task_id, $user_id);
        if (!$task) { header("Location: index.php?page=dashboard"); exit; }
        if (empty($title)) {
            $task['title'] = $_POST['title'];
            $task['description'] = $_POST['description'] ?? '';
            $this->view('tasks/edit', ['task' => $task, 'error' => "Task title is required."]);
            return;
        }
        if ($this->taskModel->update($task_id, $user_id, $title, $description)) {
            header("Location: index.php?page=dashboard");
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