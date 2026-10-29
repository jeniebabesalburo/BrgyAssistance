-- BarangayHub Resident Management System
-- Import this file using phpMyAdmin > Import.
-- Database: barangay_resident_db

CREATE DATABASE IF NOT EXISTS barangay_resident_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE barangay_resident_db;

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS residents (
    resident_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    middle_name VARCHAR(80) NULL,
    last_name VARCHAR(80) NOT NULL,
    sex ENUM('Male','Female','Prefer not to say') NOT NULL,
    birth_date DATE NULL,
    phone VARCHAR(30) NULL,
    purok VARCHAR(80) NOT NULL,
    address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_resident_name (last_name, first_name),
    INDEX idx_purok (purok)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activity_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The setup.php file creates the admin account with PHP password_hash().
-- Run setup.php after importing this schema to create the initial admin login.
