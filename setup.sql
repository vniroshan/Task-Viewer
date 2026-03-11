-- Task Viewer Setup
-- "banana"

CREATE DATABASE IF NOT EXISTS tasks_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tasks_db;

CREATE TABLE IF NOT EXISTS tasks (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255)                        NOT NULL,
    completed  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data
INSERT INTO tasks (id, title, completed) VALUES
    (1, 'Fix homepage bug',     0),
    (2, 'Update pricing page',  1),
    (3, 'Add Stripe webhook',   0),
    (4, 'Write documentation',  0);
