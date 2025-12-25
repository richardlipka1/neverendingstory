-- Create database
CREATE DATABASE IF NOT EXISTS neverendingstory;
USE neverendingstory;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    position_x INT NOT NULL,
    position_y INT NOT NULL,
    current_position BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_position (position_x, position_y)
);

-- Create index for faster lookups
CREATE INDEX idx_user_current ON rooms(user_id, current_position);
CREATE INDEX idx_position ON rooms(position_x, position_y);
