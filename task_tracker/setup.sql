-- ============================================================
-- setup.sql — Database Setup Script
--
-- HOW TO RUN:
-- Option A (phpMyAdmin):
--   1. Go to http://localhost/phpmyadmin
--   2. Click the "SQL" tab
--   3. Paste this entire file → click Go
--
-- Option B (Railway Query tab):
--   1. Railway Dashboard → MySQL service → Data tab → Query
--   2. Paste this entire file → click Run
-- ============================================================

CREATE DATABASE IF NOT EXISTS task_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE task_tracker;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    avatar     VARCHAR(500)  NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    title       VARCHAR(255)  NOT NULL,
    description TEXT,
    due_date    DATE          NULL,
    status      ENUM('pending','complete') DEFAULT 'pending',
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── OPTIONAL TEST DATA ────────────────────────────────────────
-- Uncomment below to pre-load a test account.
-- Password is: password123

/*
INSERT INTO users (name, email, password) VALUES (
    'Test User', 'test@email.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);
INSERT INTO tasks (user_id, title, description, status) VALUES
    (1, 'Buy groceries',     'Milk, eggs, bread',           'pending'),
    (1, 'Finish CST5 project', 'Task Tracker Final Project','pending'),
    (1, 'Review CSS notes',  NULL,                          'complete'),
    (1, 'Set up XAMPP',      'Install and configure',       'complete');
*/
