-- TypeMaster Database Schema
-- Modern Typing Speed Test Application

CREATE DATABASE IF NOT EXISTS typemaster_db;
USE typemaster_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    total_tests INT DEFAULT 0,
    total_time_typing INT DEFAULT 0, -- in seconds
    best_wpm DECIMAL(6,2) DEFAULT 0,
    avg_wpm DECIMAL(6,2) DEFAULT 0,
    avg_accuracy DECIMAL(5,2) DEFAULT 0,
    current_streak INT DEFAULT 0,
    max_streak INT DEFAULT 0,
    last_test_date DATE NULL,
    achievements JSON DEFAULT '[]',
    preferences JSON DEFAULT '{"theme": "dark", "sound": true, "strictMode": false}',
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_best_wpm (best_wpm DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Test Results Table
CREATE TABLE IF NOT EXISTS test_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- NULL for guest users
    wpm DECIMAL(6,2) NOT NULL,
    raw_wpm DECIMAL(6,2) NOT NULL,
    cpm INT NOT NULL,
    accuracy DECIMAL(5,2) NOT NULL,
    total_characters INT NOT NULL,
    correct_characters INT NOT NULL,
    incorrect_characters INT NOT NULL,
    total_errors INT NOT NULL,
    test_duration INT NOT NULL, -- in seconds
    test_mode VARCHAR(20) NOT NULL, -- 'time' or 'words'
    test_value INT NOT NULL, -- seconds or word count
    difficulty VARCHAR(20) NOT NULL, -- 'easy', 'medium', 'hard'
    text_type VARCHAR(20) DEFAULT 'words', -- 'words', 'sentences', 'paragraphs', 'code'
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    error_map JSON DEFAULT '{}', -- character -> error count
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_wpm (wpm DESC),
    INDEX idx_completed_at (completed_at DESC),
    INDEX idx_test_mode (test_mode, test_value)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Second-by-second metrics for analytics
CREATE TABLE IF NOT EXISTS test_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    second_mark INT NOT NULL,
    wpm_at_second DECIMAL(6,2) NOT NULL,
    accuracy_at_second DECIMAL(5,2) NOT NULL,
    characters_typed INT NOT NULL,
    errors_at_second INT NOT NULL,
    FOREIGN KEY (result_id) REFERENCES test_results(id) ON DELETE CASCADE,
    INDEX idx_result_id (result_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Text Sets for typing tests
CREATE TABLE IF NOT EXISTS text_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    difficulty VARCHAR(20) NOT NULL, -- 'easy', 'medium', 'hard'
    category VARCHAR(50) NOT NULL, -- 'common', 'punctuation', 'numbers', 'code', 'quotes'
    language VARCHAR(10) DEFAULT 'en',
    word_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    times_used INT DEFAULT 0,
    INDEX idx_difficulty (difficulty),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Achievements Definition
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL, -- emoji or icon class
    requirement_type VARCHAR(50) NOT NULL, -- 'wpm', 'accuracy', 'tests', 'streak', 'time'
    requirement_value INT NOT NULL,
    requirement_operator VARCHAR(10) DEFAULT '>=', -- '>=', '=', '<='
    rarity VARCHAR(20) DEFAULT 'common', -- 'common', 'rare', 'epic', 'legendary'
    xp_reward INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Achievements (many-to-many)
CREATE TABLE IF NOT EXISTS user_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    achievement_id INT NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_achievement (user_id, achievement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily Challenges
CREATE TABLE IF NOT EXISTS daily_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_date DATE NOT NULL UNIQUE,
    text_content TEXT NOT NULL,
    difficulty VARCHAR(20) NOT NULL,
    target_wpm INT NOT NULL,
    target_accuracy DECIMAL(5,2) NOT NULL,
    bonus_xp INT DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_challenge_date (challenge_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily Challenge Completions
CREATE TABLE IF NOT EXISTS challenge_completions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    wpm_achieved DECIMAL(6,2) NOT NULL,
    accuracy_achieved DECIMAL(5,2) NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES daily_challenges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_challenge (user_id, challenge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leaderboard Cache (for performance)
CREATE TABLE IF NOT EXISTS leaderboard_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(20) NOT NULL, -- 'global', 'daily', 'weekly', 'monthly'
    test_mode VARCHAR(20) DEFAULT 'all',
    data JSON NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_leaderboard (type, test_mode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Sessions (for enhanced security)
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
