# 📚 Website Quản Lý & Bán Sách Online - Nhóm 2

Dự án được xây dựng trên nền tảng **PHP** theo mô hình kiến trúc MVC của **Laminas Framework (tiền thân là Zend Framework)**.

---

## 👥 Danh Sách Thành Viên & Phân Công Nhiệm Vụ

| STT | Họ và Tên | Vai Trò / Nhiệm Vụ Chính | GitHub Account |
|:---:|:---|:---|:---|
| 1 | **Nguyễn Như Ngọc** | Trưởng nhóm / Quản lý Repository & Luồng chính | [@Ngnhngoc](https://github.com/Ngnhngoc) |
| 2 | **Chung Mỹ Linh** | Cơ sở dữ liệu, Model & Kết nối CSDL Laminas | [@chungmylinh55](https://github.com/chungmylinh55) |
| 3 | **Nguyễn Thị Như Quỳnh** | Thiết kế giao diện & Views người dùng | [@nguyenquynh](https://github.com/nguyenquynh) |
| 4 | **Phạm Thị Hồng Phương** | Xử lý chức năng & Controllers | [@hphuong010905](https://github.com/hphuong010905) |
| 5 | **Trần Nhật Duy** | Xây dựng chức năng Xóa, xác nhận xóa, xử lý lỗi & Kiểm thử CRUD | [@NHATDUY119](https://github.com/NHATDUY119) |

### 📋 Chi tiết phân công công việc các module:
- **Phần 1:** Xây dựng Controller, Routing, hiển thị Danh sách Loại Sách và tính năng Tìm kiếm *(Phần đã hoàn thiện trong nhánh này)*.
- **Phần 2:** Xây dựng chức năng Thêm, Sửa Loại sách và Kiểm tra dữ liệu (Validation).
- **Phần 3:** Xây dựng chức năng Xóa, Hộp thoại xác nhận xóa và Xử lý lỗi ngoại lệ.
- **Phần 4:** Xây dựng Đăng nhập, Đăng xuất, Session và Phân quyền người dùng.
- **Phần 5:** Tích hợp tổng thể, kiểm thử toàn bộ hệ thống (CRUD & Auth).

---

## 🛠️ Công Nghệ & Môi Trường Phát Triển

- **Ngôn ngữ:** PHP >= 8.1 (yêu cầu các extension: `pdo_mysql`, `mbstring`, `intl`)
- **Framework MVC:** [Laminas MVC](https://getlaminas.org/) v3.7+ (Zend Framework)
  - `laminas/laminas-db`: Thao tác cơ sở dữ liệu qua TableGateway Pattern
  - `laminas/laminas-router`: Định tuyến URL
  - `laminas/laminas-view`: Template engine và View Helpers
  - `laminas/laminas-mvc-plugin-flashmessenger`: Quản lý thông báo phiên
- **Cơ sở dữ liệu:** MySQL 8.0 / MariaDB (bảng mã: `utf8mb4_unicode_ci`)
- **Web Server:** Apache (Laragon / XAMPP) hoặc PHP Built-in Server
- **Quản lý thư viện:** Composer
- **Giao diện:** Bootstrap 5, FontAwesome 6, CSS tùy biến

---

## 📁 Cấu Trúc Thư Mục Dự Án

```text
Nhom2_Zend_BanSachOnline/
├── config/
│   ├── autoload/
│   │   ├── global.php             # Cấu hình kết nối cơ sở dữ liệu chung
│   │   └── local.php              # Cấu hình cụ thể môi trường cục bộ (host, user, pass, db)
│   ├── modules.config.php         # Danh sách các module kích hoạt trong Laminas
│   └── application.config.php     # Cấu hình nạp ứng dụng
├── data/
│   └── ban_sach_online.sql        # Script tạo CSDL và nạp dữ liệu mẫu
├── module/
│   └── Application/               # Module chính của ứng dụng
│       ├── config/
│       │   └── module.config.php  # Cấu hình Routing, Controller và DI Factories
│       ├── src/
│       │   ├── Controller/
│       │   │   ├── CategoryController.php # Controller xử lý Danh sách & Tìm kiếm
│       │   │   └── IndexController.php    # Điều hướng về danh mục chính
│       │   └── Model/
│       │       ├── LoaiSachTable.php      # Model thao tác dữ liệu bảng loaisach
│       │       ├── DauSachTable.php       # Model bảng dausach
│       │       └── NguoiDungTable.php     # Model bảng nguoidung
│       └── view/
│           ├── application/
│           │   ├── category/
│           │   │   └── index.phtml        # Giao diện Danh sách & Tìm kiếm Loại Sách
│           │   ├── index/
│           │   │   └── index.phtml        # View trang chủ
│           │   └── partial/
│           │       └── flash-messages.phtml # Template thông báo hệ thống
│           └── layout/
│               └── layout.phtml           # Khung layout chung (Navbar, Sidebar)
├── public/
│   ├── index.php                  # Điểm khởi chạy ứng dụng (Entry point)
│   ├── .htaccess                  # Cấu hình URL Rewrite của Apache
│   └── css/, js/                  # Tài nguyên tĩnh
├── .htaccess                      # Chuyển hướng request thư mục gốc vào public/
└── composer.json                  # Khai báo thư viện và scripts
```

---

## 🚀 Hướng Dẫn Cài Đặt & Khởi Chạy Dự Án

### Bước 1: Tải mã nguồn về máy
Mở Terminal hoặc Command Prompt:
```bash
git clone https://github.com/Ngnhngoc/Nhom2_Zend_BanSachOnline.git
cd Nhom2_Zend_BanSachOnline
git checkout linh
```

### Bước 2: Cài đặt thư viện dependencies
Chạy lệnh sau tại thư mục gốc:
```bash
composer install
```

### Bước 3: Cấu hình và nạp Cơ Sở Dữ Liệu
1. Mở phần mềm **Laragon** (hoặc XAMPP) và khởi động dịch vụ **MySQL**.
2. Mở công cụ quản trị CSDL (HeidiSQL, phpMyAdmin, hoặc Terminal MySQL).
3. Import file dữ liệu tại đường dẫn: `data/ban_sach_online.sql`.
   *File sẽ tự động khởi tạo database `ban_sach_online` và chèn dữ liệu mẫu.*
4. Kiểm tra cấu hình kết nối tại file `config/autoload/local.php`:
   ```php
   return [
       'db' => [
           'driver'   => 'Pdo_Mysql',
           'hostname' => 'localhost',
           'port'     => 3306,
           'database' => 'ban_sach_online',
           'username' => 'root',
           'password' => '', // Điền mật khẩu nếu MySQL của bạn có thiết lập
           'charset'  => 'utf8mb4',
       ],
   ];
   ```

### Bước 4: Chạy Website

#### Cách 1: Sử dụng Laragon (Khuyên dùng)
- Đặt thư mục dự án tại `C:\laragon\www\Nhom2_Zend_BanSachOnline`.
- Bật **Apache** và **MySQL** trên giao diện Laragon.
- Mở trình duyệt web và truy cập một trong hai địa chỉ:
  - Qua Virtual Host: **[http://nhom2_zend_bansachonline.test:8080](http://nhom2_zend_bansachonline.test:8080)**
  - Qua đường dẫn trực tiếp: **[http://localhost:8080/Nhom2_Zend_BanSachOnline/public/](http://localhost:8080/Nhom2_Zend_BanSachOnline/public/)**

#### Cách 2: Sử dụng PHP Built-in Server
Chạy lệnh sau tại thư mục dự án:
```bash
php -S localhost:8080 -t public
```
Sau đó truy cập: **[http://localhost:8080](http://localhost:8080)**

---

## 📌 Các Chức Năng Đã Xây Dựng

Phạm vi hoàn thiện bao gồm **Controller, Routing, Hiển thị Danh sách, Tìm kiếm, Thêm, Sửa, Xóa Loại Sách (CRUD) và Phân quyền**:

| Đường Dẫn (Route) | Controller | Action | Chức Năng Thực Hiện |
|:---|:---|:---|:---|
| `/` | `AuthController` | `login` | Trang đăng nhập hệ thống & kiểm tra phiên làm việc |
| `/category` | `CategoryController` | `index` | Quản lý danh mục loại sách: Hiển thị bảng danh sách động từ MySQL |
| `/category?keyword=...` | `CategoryController` | `index` | Tìm kiếm loại sách theo tên loại, mã loại (VD: `L01`), hoặc mô tả |
| `/category?trang_thai=...` | `CategoryController` | `index` | Lọc loại sách theo trạng thái: Đang hoạt động (`1`) hoặc Tạm ẩn/Khóa (`0`) |
| `/category/add` | `CategoryController` | `add` | Thêm mới loại sách, kiểm tra hợp lệ dữ liệu (Validation) |
| `/category/edit/:id` | `CategoryController` | `edit` | Chỉnh sửa loại sách, cập nhật mô tả và trạng thái hoạt động/khóa |
| `/category/delete/:id` | `CategoryController` | `delete` | Xóa loại sách: Xác nhận qua Modal Bootstrap 5, bảo vệ phân quyền Admin, bắt lỗi ngoại lệ khóa ngoại và bảo toàn bộ lọc tìm kiếm |

### ✨ Điểm nổi bật của tính năng:
- **Truy vấn CSDL động:** Dữ liệu được nạp trực tiếp từ bảng `loaisach` thông qua `LoaiSachTable` (mô hình TableGateway).
- **Bộ lọc tìm kiếm linh hoạt:**
  - Hỗ trợ tìm kiếm theo tên loại sách (ví dụ: *Công nghệ*, *Văn học*...).
  - Hỗ trợ tìm kiếm theo mã định danh (nhập `L01`, `L1`, hoặc số `1`).
  - Lọc nhanh trạng thái hoạt động hoặc tạm ẩn.
  - Nút **Làm mới (Reset bộ lọc)** giúp người dùng quay lại danh sách ban đầu nhanh chóng.
- **Xác nhận xóa & An toàn dữ liệu:**
  - Hộp thoại Modal Bootstrap 5 trực quan, hiển thị rõ mã và tên loại sách trước khi xác nhận.
  - Ngăn chặn lỗi vi phạm khóa ngoại (Foreign Key Constraint `ON DELETE RESTRICT`) khi loại sách đang có đầu sách liên kết.
  - Bảo toàn trạng thái bộ lọc (Query Parameters) sau khi thực hiện thao tác xóa.
- **Giao diện chuẩn hóa:** Bố cục hiện đại với Bootstrap 5, Badge phân loại trạng thái, Sidebar danh mục được tối ưu hiển thị.

### 🧪 Kết quả kiểm thử toàn diện CRUD & Phân quyền:
- **Create (Thêm mới):** Thêm thành công loại sách hợp lệ; validation chặn tên rỗng, tên quá dài, trùng tên.
- **Read (Xem & Tìm kiếm):** Lọc theo trạng thái và tìm kiếm từ khóa hoạt động chính xác.
- **Update (Chỉnh sửa):** Cập nhật dữ liệu thành công, bảo toàn khóa chính ID.
- **Delete (Xóa dữ liệu):**
  - Xóa thành công loại sách rỗng.
  - Bắt lỗi an toàn và thông báo thân thiện khi xóa loại sách có sách liên kết (không để lỗi 500).
  - Phân quyền chặt chẽ: Chỉ tài khoản `Admin` được xóa, tài khoản `NhanVien` bị từ chối truy cập.

---

## 💡 Các Lệnh Tiện Ích Với Composer

- **Xóa bộ nhớ đệm cấu hình (Config Cache):**
  ```bash
  composer clear-config-cache
  ```
- **Bật chế độ phát triển (Development Mode):**
  ```bash
  composer development-enable
  ```
- **Tắt chế độ phát triển:**
  ```bash
  composer development-disable
  ```
