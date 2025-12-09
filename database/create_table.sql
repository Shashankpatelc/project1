-- create wellness_tracker database
CREATE DATABASE IF NOT EXISTS wellness_tracker_db;
USE wellness_tracker_db;

-- create users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- create mood_entries table
CREATE TABLE mood_entries (
    entry_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mood_score TINYINT NOT NULL CHECK (mood_score BETWEEN 0 AND 10),
    stress_score TINYINT NOT NULL CHECK (stress_score BETWEEN 0 AND 10),
    notes TEXT,
    entry_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, entry_date),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- create coping_resources table
CREATE TABLE coping_resources (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    sort_order TINYINT DEFAULT 0
);

