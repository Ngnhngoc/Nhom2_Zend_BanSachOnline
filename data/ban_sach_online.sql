CREATE DATABASE IF NOT EXISTS ban_sach_online
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ban_sach_online;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_code VARCHAR(20) NOT NULL,
    category_name VARCHAR(150) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_code (category_code),
    UNIQUE KEY uq_categories_name (category_name)
) ENGINE=InnoDB;

INSERT INTO categories (category_code, category_name, status)
VALUES
    ('L01', 'Công nghệ thông tin', 1),
    ('L02', 'Văn học nghệ thuật', 1)
ON DUPLICATE KEY UPDATE category_code = VALUES(category_code);
