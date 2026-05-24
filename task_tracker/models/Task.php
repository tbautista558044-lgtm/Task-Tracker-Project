<?php
// ============================================================
// models/Task.php — Task Model
//
// WHAT IT DOES:
// Contains ALL SQL queries related to the "tasks" table.
// Every CRUD operation for tasks goes through this class.
// The Controller never writes SQL — it calls these methods.
//
// Methods in this model:
//   getAllByUser()    → READ   (dashboard task list)
//   getById()        → READ   (edit form pre-fill)
//   create()         → CREATE (add task)
//   update()         → UPDATE (edit task)
//   updateStatus()   → UPDATE (mark complete / pending)
//   delete()         → DELETE (remove task)
// ============================================================

class Task {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ──────────────────────────────────────────────────────────
    // getAllByUser($user_id)
    //
    // Returns all tasks for a specific user, newest first.
    // The WHERE user_id = ? ensures users only see their OWN tasks.
    //
    // USED BY: TaskController → dashboard()
    // ──────────────────────────────────────────────────────────
    public function getAllByUser($user_id) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC"
        );
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC); // Returns array of rows
    }

    // ──────────────────────────────────────────────────────────
    // getById($task_id, $user_id)
    //
    // Fetches ONE task by its ID.
    // IMPORTANT: We also check user_id so a user can't access
    // someone else's task by guessing the ID in the URL.
    //
    // USED BY: TaskController → editForm(), edit()
    // ──────────────────────────────────────────────────────────
    public function getById($task_id, $user_id) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT * FROM tasks WHERE id = ? AND user_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result); // One row or NULL
    }

    // ──────────────────────────────────────────────────────────
    // create($user_id, $title, $description)
    //
    // Inserts a new task row. Status defaults to 'pending' in DB.
    // Returns TRUE on success, FALSE on failure.
    //
    // USED BY: TaskController → add()
    // ──────────────────────────────────────────────────────────
    public function create($user_id, $title, $description) {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $description);
        return mysqli_stmt_execute($stmt);
    }

    // ──────────────────────────────────────────────────────────
    // update($task_id, $user_id, $title, $description)
    //
    // Updates title and description of an existing task.
    // Double WHERE (id AND user_id) = ownership check.
    //
    // USED BY: TaskController → edit()
    // ──────────────────────────────────────────────────────────
    public function update($task_id, $user_id, $title, $description) {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ssii", $title, $description, $task_id, $user_id);
        return mysqli_stmt_execute($stmt);
    }

    // ──────────────────────────────────────────────────────────
    // updateStatus($task_id, $user_id, $status)
    //
    // Changes status to either 'complete' or 'pending'.
    // $status must be one of the ENUM values from the DB schema.
    //
    // USED BY: TaskController → markComplete(), markPending()
    // ──────────────────────────────────────────────────────────
    public function updateStatus($task_id, $user_id, $status) {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sii", $status, $task_id, $user_id);
        return mysqli_stmt_execute($stmt);
    }

    // ──────────────────────────────────────────────────────────
    // delete($task_id, $user_id)
    //
    // Permanently removes a task row from the database.
    // Again, user_id check prevents deleting others' tasks.
    //
    // USED BY: TaskController → delete()
    // ──────────────────────────────────────────────────────────
    public function delete($task_id, $user_id) {
        $stmt = mysqli_prepare($this->conn,
            "DELETE FROM tasks WHERE id = ? AND user_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
        return mysqli_stmt_execute($stmt);
    }
}
?>
