-- =========================================================
-- DU AN: Quan ly loai sach / Ban sach online
-- Database: ban_sach_online
--
-- Cach su dung:
-- 1. Mo XAMPP, khoi dong MySQL.
-- 2. Mo phpMyAdmin -> Import -> chon file SQL nay.
-- 3. File se tao database/bang neu chua co va bo sung du lieu mau con thieu.
--
-- Luu y:
-- - File KHONG xoa database hoac du lieu hien co.
-- - Tai khoan ung dung mau: testuser
--   Mat khau thu nghiem: password
-- - Mat khau duoc luu duoi dang bcrypt hash.
-- =========================================================

CREATE DATABASE IF NOT EXISTS ban_sach_online
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ban_sach_online;

CREATE TABLE IF NOT EXISTS loai_sach (
    id INT NOT NULL AUTO_INCREMENT,
    ten_loai VARCHAR(150) NOT NULL,
    mo_ta TEXT NULL,
    trang_thai TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nguoi_dung (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'NhanVien') NOT NULL DEFAULT 'NhanVien',
    PRIMARY KEY (id),
    UNIQUE KEY uq_nguoi_dung_username (username)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Bo sung tung loai sach neu chua ton tai.
-- Cach nay van them duoc loai con thieu neu bang da co mot vai dong.
INSERT INTO loai_sach (ten_loai, mo_ta, trang_thai)
SELECT 'Công nghệ thông tin', 'Sách lập trình và công nghệ', 1
WHERE NOT EXISTS (
    SELECT 1 FROM loai_sach WHERE ten_loai = 'Công nghệ thông tin'
);

INSERT INTO loai_sach (ten_loai, mo_ta, trang_thai)
SELECT 'Văn học', 'Sách văn học trong nước và nước ngoài', 1
WHERE NOT EXISTS (
    SELECT 1 FROM loai_sach WHERE ten_loai = 'Văn học'
);

INSERT INTO loai_sach (ten_loai, mo_ta, trang_thai)
SELECT 'Kinh tế', 'Sách kinh doanh và quản trị', 1
WHERE NOT EXISTS (
    SELECT 1 FROM loai_sach WHERE ten_loai = 'Kinh tế'
);

INSERT INTO loai_sach (ten_loai, mo_ta, trang_thai)
SELECT 'Kỹ năng sống', 'Sách phát triển bản thân', 1
WHERE NOT EXISTS (
    SELECT 1 FROM loai_sach WHERE ten_loai = 'Kỹ năng sống'
);

-- Tao tai khoan thu nghiem neu username chua ton tai.
-- Username: testuser
-- Password: password
-- Role: NhanVien
INSERT INTO nguoi_dung (username, password, role)
SELECT
    'testuser',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'NhanVien'
WHERE NOT EXISTS (
    SELECT 1 FROM nguoi_dung WHERE username = 'testuser'
);

-- Kiem tra du lieu sau khi import:
SELECT id, ten_loai, mo_ta, trang_thai FROM loai_sach ORDER BY id;
SELECT id, username, role FROM nguoi_dung ORDER BY id;
