<?php
// controllers/AccountController.php — Account + Profile Controller

class AccountController {

    private $conn;
    private $accountModel;

    public function __construct($conn) {
        $this->conn         = $conn;
        $this->accountModel = new Account($conn);
    }

    // ── login ────────────────────────────────────────────────
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?page=dashboard"); exit;
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim(strip_tags($_POST['email']));
            $password = $_POST['password'];
            if (empty($email) || empty($password)) {
                $error = "Please fill in all fields.";
            } else {
                $user = $this->accountModel->findByEmail($email);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id']     = $user['id'];
                    $_SESSION['user_name']   = $user['name'];
                    $_SESSION['user_avatar'] = $user['avatar'] ?? '';
                    header("Location: index.php?page=dashboard"); exit;
                } else {
                    $error = "Invalid email or password.";
                }
            }
        }
        $this->view('auth/login', ['error' => $error]);
    }

    // ── register ─────────────────────────────────────────────
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?page=dashboard"); exit;
        }
        $error = ''; $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name     = trim(strip_tags($_POST['name']));
            $email    = trim(strip_tags($_POST['email']));
            $password = $_POST['password'];
            $confirm  = $_POST['confirm'];
            if (empty($name) || empty($email) || empty($password)) {
                $error = "All fields are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
            } elseif (strlen($password) < 6) {
                $error = "Password must be at least 6 characters.";
            } elseif ($password !== $confirm) {
                $error = "Passwords do not match.";
            } elseif ($this->accountModel->emailExists($email)) {
                $error = "An account with that email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                if ($this->accountModel->create($name, $email, $hashed)) {
                    $success = "Account created! You can now log in.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
        $this->view('auth/register', ['error' => $error, 'success' => $success]);
    }

    // ── logout ───────────────────────────────────────────────
    public function logout() {
        session_unset(); session_destroy();
        header("Location: index.php?page=login"); exit;
    }

    // ── profile (GET + POST) ─────────────────────────────────
    public function profile() {
        $this->requireLogin();
        $user_id = $_SESSION['user_id'];
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'upload_avatar') {
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
                    $mime    = mime_content_type($_FILES['avatar']['tmp_name']);
                    if (in_array($mime, $allowed) && $_FILES['avatar']['size'] <= 2 * 1024 * 1024) {
                        $ext      = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                        $filename = 'avatar_' . $user_id . '_' . time() . '.' . strtolower($ext);
                        $dest     = ROOT . '/public/avatars/' . $filename;
                        if (!is_dir(ROOT . '/public/avatars')) {
                            mkdir(ROOT . '/public/avatars', 0755, true);
                        }
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                            $this->accountModel->updateAvatar($user_id, $filename);
                            $_SESSION['user_avatar'] = $filename;
                            $success = "Profile photo updated! ✦";
                        } else {
                            $error = "Could not save the image.";
                        }
                    } else {
                        $error = "Please upload a JPG, PNG, GIF or WEBP under 2MB.";
                    }
                }
            } elseif ($action === 'update_info') {
                $name  = trim(strip_tags($_POST['name']  ?? ''));
                $email = trim(strip_tags($_POST['email'] ?? ''));
                if (empty($name) || empty($email)) {
                    $error = "Name and email are required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Please enter a valid email address.";
                } elseif ($this->accountModel->emailExistsForOther($email, $user_id)) {
                    $error = "That email is already used by another account.";
                } else {
                    if ($this->accountModel->updateInfo($user_id, $name, $email)) {
                        $_SESSION['user_name'] = $name;
                        $success = "Profile updated successfully! ✦";
                    } else {
                        $error = "Could not update profile. Please try again.";
                    }
                }

            } elseif ($action === 'change_password') {
                $current = $_POST['current_password'] ?? '';
                $newpw   = $_POST['new_password']     ?? '';
                $confirm = $_POST['confirm_password'] ?? '';
                $user    = $this->accountModel->findById($user_id);
                if (!password_verify($current, $user['password'])) {
                    $error = "Current password is incorrect.";
                } elseif (strlen($newpw) < 6) {
                    $error = "New password must be at least 6 characters.";
                } elseif ($newpw !== $confirm) {
                    $error = "New passwords do not match.";
                } else {
                    $hashed = password_hash($newpw, PASSWORD_DEFAULT);
                    if ($this->accountModel->updatePassword($user_id, $hashed)) {
                        $success = "Password changed successfully! 🔒";
                    } else {
                        $error = "Could not update password. Please try again.";
                    }
                }
            }
        }

        $user = $this->accountModel->findById($user_id);
        $this->view('profile/index', [
            'user'    => $user,
            'error'   => $error,
            'success' => $success,
        ]);
    }

    // ── delete account ───────────────────────────────────────
    public function deleteAccount() {
        $this->requireLogin();
        $user_id = $_SESSION['user_id'];
        $this->accountModel->deleteUser($user_id);
        session_unset(); session_destroy();
        header("Location: index.php?page=login"); exit;
    }

    // ── private helpers ──────────────────────────────────────
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login"); exit;
        }
    }

    private function view($name, $data = []) {
        extract($data);
        require ROOT . "/views/{$name}.php";
    }
}
?>