
USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date = DATE(created_at) WHERE due_date IS NULL;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Add start_date and end_date columns for date range support
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS start_date DATE NULL AFTER due_date;
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS end_date DATE NULL AFTER start_date;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date    = DATE(created_at) WHERE due_date    IS NULL;
UPDATE tasks SET start_date  = due_date          WHERE start_date IS NULL;
UPDATE tasks SET end_date    = due_date          WHERE end_date   IS NULL;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date = DATE(created_at) WHERE due_date IS NULL;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Add start_date and end_date columns for date range support
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS start_date DATE NULL AFTER due_date;
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS end_date DATE NULL AFTER start_date;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date    = DATE(created_at) WHERE due_date    IS NULL;
UPDATE tasks SET start_date  = due_date          WHERE start_date IS NULL;
UPDATE tasks SET end_date    = due_date          WHERE end_date   IS NULL;

-- Add priority and category columns to tasks
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium' AFTER status;
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS category VARCHAR(50) NULL AFTER priority;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date = DATE(created_at) WHERE due_date IS NULL;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Add start_date and end_date columns for date range support
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS start_date DATE NULL AFTER due_date;
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS end_date DATE NULL AFTER start_date;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date    = DATE(created_at) WHERE due_date    IS NULL;
UPDATE tasks SET start_date  = due_date          WHERE start_date IS NULL;
UPDATE tasks SET end_date    = due_date          WHERE end_date   IS NULL;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date = DATE(created_at) WHERE due_date IS NULL;

USE task_tracker;

-- Add avatar column to users (stores the uploaded photo filename)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(500) NULL AFTER password;

-- Add due_date column to tasks (the date the task is scheduled for)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS due_date DATE NULL AFTER description;

-- Add start_date and end_date columns for date range support
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS start_date DATE NULL AFTER due_date;
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS end_date DATE NULL AFTER start_date;

-- Backfill existing tasks so they show up on the calendar correctly
UPDATE tasks SET due_date    = DATE(created_at) WHERE due_date    IS NULL;
UPDATE tasks SET start_date  = due_date          WHERE start_date IS NULL;
UPDATE tasks SET end_date    = due_date          WHERE end_date   IS NULL;

-- Add priority and category columns to tasks
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium' AFTER status;
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS category VARCHAR(50) NULL AFTER priority;
-- Add icon column to tasks (stores a single emoji chosen by the user)
ALTER TABLE tasks
    ADD COLUMN IF NOT EXISTS icon VARCHAR(10) NULL AFTER category;
