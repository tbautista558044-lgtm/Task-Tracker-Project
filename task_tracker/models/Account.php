<?php
// models/Account.php — User Account Model

class Account {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Find by email (for login)
    public function findByEmail($email) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT id, name, email, password, created_at FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    // Find by ID (for profile)
    public function findById($id) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT id, name, email, password, created_at FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    // Check if email exists (registration)
    public function emailExists($email) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        return mysqli_stmt_num_rows($stmt) > 0;
    }

    // Check if email is taken by someone else (profile update)
    public function emailExistsForOther($email, $user_id) {
        $stmt = mysqli_prepare($this->conn,
            "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        return mysqli_stmt_num_rows($stmt) > 0;
    }

    // Create account (registration)
    public function create($name, $email, $hashedPassword) {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);
        return mysqli_stmt_execute($stmt);
    }

    // Update name + email (profile)
    public function updateInfo($user_id, $name, $email) {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE users SET name = ?, email = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
        return mysqli_stmt_execute($stmt);
    }

    // Update password (profile)
    public function updatePassword($user_id, $hashedPassword) {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $user_id);
        return mysqli_stmt_execute($stmt);
    }

    // Delete user (and cascade-delete tasks via FK)
    public function deleteUser($user_id) {
        $stmt = mysqli_prepare($this->conn,
            "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        return mysqli_stmt_execute($stmt);
    }
}
?>