# Book Management – Laminas MVC

Ứng dụng quản lý và bán sách của Nhóm 2, xây dựng bằng Laminas MVC. Bài thực hành tập trung vào CRUD bảng Loại sách.

## Yêu cầu

- PHP: 8.1, 8.2 hoặc 8.3; môi trường thực hành sử dụng PHP 8.3.27.
- Các extension PHP: `pdo_mysql`, `mbstring`, `intl`.
- Composer: 2.x; môi trường thực hành sử dụng Composer 2.4.1.
- MySQL: 8.0; môi trường thực hành sử dụng MySQL 8.0.30.
- Môi trường: Laragon trên Windows. PHP và Composer cần được nhận diện trong Terminal.

## Cài đặt

Thực hiện các lệnh tại thư mục gốc dự án bằng Git Bash.

1. Cài đặt các thư viện:

   ```bash
   composer install
   ```

2. Sao chép file cấu hình mẫu thành `config/autoload/local.php` nếu chưa có:

   ```bash
   cp -n config/autoload/local.php.dist config/autoload/local.php
   ```

3. Bật MySQL trong Laragon. Với lần cài đặt đầu tiên, nhập file `data/ban_sach_online.sql` bằng HeidiSQL hoặc phpMyAdmin. File SQL tạo cơ sở dữ liệu `ban_sach_online`, các bảng và dữ liệu mẫu.

4. Mở `config/autoload/local.php` và cấu hình các giá trị trong mảng `db`:
   - `hostname`: địa chỉ MySQL, ví dụ `localhost`.
   - `port`: cổng MySQL, mặc định `3306`.
   - `database`: `ban_sach_online`.
   - `username`, `password`: tài khoản và mật khẩu MySQL trên máy.

5. Khởi chạy ứng dụng:

   ```bash
   composer serve
   ```

   Có thể chạy trực tiếp bằng PHP thay cho lệnh trên:

   ```bash
   php -S 127.0.0.1:8080 -t public
   ```

6. Mở trang chủ tại http://127.0.0.1:8080. Đăng nhập tại http://127.0.0.1:8080/auth/login bằng tài khoản Admin để truy cập trang quản lý Loại sách tại http://127.0.0.1:8080/category.

## Chức năng

- Hiển thị danh sách, tìm kiếm và lọc Loại sách theo trạng thái.
- Thêm, sửa Loại sách; kiểm tra dữ liệu đầu vào và tên bị trùng.
- Xóa Loại sách có hộp thoại xác nhận; xử lý lỗi khi còn đầu sách liên kết.
- Đăng nhập, đăng xuất, quản lý Session và kiểm tra quyền Admin khi truy cập chức năng quản lý Loại sách.
- Xem, tìm kiếm và lọc đầu sách trên trang chủ.

## Thành viên

- **Nguyễn Như Ngọc** – Cấu hình dự án Laminas MVC; xây dựng Controller và Routing cho chức năng hiển thị danh sách, tìm kiếm Loại sách.
- **Chung Mỹ Linh** – Thiết kế CSDL MySQL; kết nối Laminas với MySQL; xây dựng Model cho bảng Loại sách và Người dùng; tạo dữ liệu mẫu.
- **Nguyễn Thị Như Quỳnh** – Thiết kế giao diện chung bằng Bootstrap; xây dựng giao diện đăng nhập, danh sách Loại sách, biểu mẫu thêm/sửa và thông báo kết quả.
- **Trần Kim Tài** – Xây dựng chức năng đăng nhập, đăng xuất; quản lý Session; xác thực tài khoản và phân quyền Admin/Nhân viên.
- **Phạm Thị Hồng Phương** – Xây dựng chức năng thêm, sửa Loại sách; kiểm tra dữ liệu đầu vào; xử lý trùng tên và các trường hợp dữ liệu không hợp lệ.
- **Trần Nhật Duy** – Xây dựng chức năng xóa Loại sách; xác nhận xóa; xử lý lỗi khi xóa; lập trình kiểm thử tự động cho các chức năng CRUD.
