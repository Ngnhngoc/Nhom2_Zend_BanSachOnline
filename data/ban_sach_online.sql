-- TAO CSDL MOI
CREATE DATABASE ban_sach_online
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ban_sach_online;

-- =========================================
-- 1. BANG LOAI SACH
-- Bo sung ngay_tao va ngay_cap_nhat
-- =========================================
CREATE TABLE LoaiSach (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten_loai VARCHAR(150) NOT NULL,
    mo_ta TEXT NULL,
    trang_thai TINYINT(1) NOT NULL DEFAULT 1,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq__ten (ten_loai)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 2. BANG DAU SACH
-- Moi dong la mot dau sach, so_luong la so cuon
-- =========================================
CREATE TABLE DauSach (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten_sach VARCHAR(255) NOT NULL,
    tac_gia VARCHAR(150) NULL,
    nha_xuat_ban VARCHAR(150) NULL,
    gia_ban DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    so_luong INT UNSIGNED NOT NULL DEFAULT 0,
    _id INT UNSIGNED NOT NULL,

    INDEX idx_dau_sach_loai (loai_sach_id),

    CONSTRAINT fk_dau_sach_loai
        FOREIGN KEY (loai_sach_id)
        REFERENCES LoaiSach(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 3. BANG NGUOI DUNG
-- Theo so do, khong them ngay_tao/ngay_cap_nhat
-- =========================================
CREATE TABLE NguoiDung (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'NhanVien',
    ho_ten VARCHAR(150) NULL,
    email VARCHAR(150) NULL,
    so_dien_thoai VARCHAR(20) NULL,
    dia_chi VARCHAR(255) NULL,
    trang_thai TINYINT(1) NOT NULL DEFAULT 1,

    UNIQUE KEY uq_nguoi_dung_username (username),
    UNIQUE KEY uq_nguoi_dung_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 4. DU LIEU MAU CHO LOAI SACH
-- =========================================
INSERT INTO LoaiSach (ten_loai, mo_ta, trang_thai)
VALUES
('Cong nghe thong tin', 'Sach lap trinh va cong nghe', 1),
('Van hoc', 'Sach van hoc trong nuoc va nuoc ngoai', 1),
('Kinh te', 'Sach kinh te va kinh doanh', 1),
('Ky nang song', 'Sach phat trien ban than', 1);

-- =========================================
-- 5. DU LIEU MAU CHO DAU SACH
-- =========================================
INSERT INTO DauSach
    (ten_sach, tac_gia, nha_xuat_ban, gia_ban, so_luong, loai_sach_id)
VALUES
('Lap trinh PHP', 'Nguyen Van A', 'NXB Tre', 85000, 20, 1),
('Co so du lieu', 'Tran Van B', 'NXB Giao Duc', 95000, 15, 1),
('De Men phieu luu ky', 'To Hoai', 'NXB Kim Dong', 60000, 10, 2),
('Dac nhan tam', 'Dale Carnegie', 'NXB Tong Hop', 75000, 12, 4);


-- =========================================
-- 6. THEM DU LIEU MAU CHO NGUOI DUNG
-- =========================================
INSERT INTO NguoiDung (
    username,
    password,
    role,
    ho_ten,
    email,
    so_dien_thoai,
    dia_chi,
    trang_thai
)
VALUES (
    'testuser',
    '$2y$10$92IXUNjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'NhanVien',
    'Nguyễn Văn A',
    'testuser@example.com',
    '0901234567',
    'TP. Hồ Chí Minh',
    1
);

-- =========================================
-- 7. KIEM TRA SO DAU SACH VA TONG SO CUON THEO LOAI
-- =========================================
SELECT
    ls.id,
    ls.ten_loai,
    COUNT(ds.id) AS so_dau_sach,
    COALESCE(SUM(ds.so_luong), 0) AS tong_so_cuon
FROM LoaiSach ls
LEFT JOIN DauSach ds
    ON ls.id = ds.loai_sach_id
GROUP BY
    ls.id,
    ls.ten_loai
ORDER BY ls.id;
