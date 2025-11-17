# Hệ Thống Quản Lý Tour Du Lịch

Hệ thống quản lý tour du lịch được xây dựng bằng PHP thuần, hỗ trợ quản lý tour, đặt tour, báo cáo doanh thu và các chức năng liên quan.

## Yêu Cầu Hệ Thống

- PHP >= 7.4
- MySQL >= 5.7 hoặc MariaDB >= 10.3
- Apache với mod_rewrite hoặc Nginx
- Composer (tùy chọn, cho autoloading)

## Cài Đặt

### 1. Clone hoặc tải dự án về máy
tao branch moi
git checkout -b feature/my-feature
chuyen sang main
git checkout main
cap nhat code moi nhat
git fetch origin
git pull origin main
quay lai branch dang lam
git checkout feature/my-feature
gop code vao main
git merge main
push len git
git push origin feature/my-feature

### 2. Cấu hình Database

- Import file `duan1.sql` vào MySQL/MariaDB để tạo database và các bảng
- Cập nhật thông tin database trong file `env.php`:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USERNAME', 'root');
  define('DB_PASSWORD', '');
  define('DB_NAME', 'duan1');
  ```

### 3. Cấu hình Web Server

#### Apache
- Đảm bảo mod_rewrite đã được bật
- Document root trỏ đến thư mục `public/`
- File `.htaccess` đã có sẵn trong thư mục `public/`

#### Nginx
Cấu hình tương tự:
```nginx
location / {
    try_files $uri $uri/ /index.php?url=$uri&$args;
}
```

### 4. Cấu hình URL

Cập nhật `BASE_URL` trong file `env.php` theo domain của bạn:
```php
define('BASE_URL', 'http://localhost/duan1/');
```

### 5. Cài đặt Composer (Tùy chọn)

```bash
composer install
```

## Cấu Trúc Dự Án

```
duan1/
├── app/
│   ├── controllers/     # Controllers xử lý logic
│   ├── models/          # Models tương tác với database
│   └── core/            # Core classes (Database, Helper, Session)
├── configs/             # File cấu hình (database, routes)
├── public/              # Thư mục public (entry point)
│   ├── assets/          # CSS, JS, images, uploads
│   └── index.php        # Entry point chính
├── views/               # View files (templates)
├── env.php              # Environment configuration
├── composer.json        # Composer dependencies
└── duan1.sql           # Database schema
```

## Chức Năng Chính

### 1. Quản Lý Tour
- Xem danh sách tour
- Thêm mới tour
- Sửa thông tin tour
- Xóa tour
- Quản lý loại tour

### 2. Quản Lý Đặt Tour (Booking)
- Xem danh sách đặt tour
- Tạo đặt tour mới
- Cập nhật trạng thái đặt tour
- Quản lý khách hàng

### 3. Báo Cáo
- Báo cáo doanh thu theo thời gian
- Báo cáo lợi nhuận
- Thống kê số lượng đặt tour

### 4. Xác Thực Người Dùng
- Đăng nhập
- Đăng xuất
- Quản lý session

## Tài Khoản Mặc Định

Sau khi import database, bạn có thể đăng nhập với:
- **Username:** admin
- **Password:** password (mặc định, nên đổi sau khi cài đặt)

## Routes

Các routes chính được định nghĩa trong `configs/routes.php`:

- `/` - Trang chủ
- `/login` - Đăng nhập
- `/logout` - Đăng xuất
- `/tour` - Danh sách tour
- `/tour/create` - Tạo tour mới
- `/booking` - Danh sách đặt tour
- `/report` - Báo cáo doanh thu

## Phát Triển

### Thêm Route Mới

Thêm route vào `configs/routes.php`:
```php
'route-name' => ['ControllerName', 'methodName'],
```

### Thêm Model Mới

Tạo file trong `app/models/` và kế thừa từ Database class:
```php
require_once __DIR__ . '/../core/Database.php';

class YourModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::connect();
    }
}
```

### Thêm Controller Mới

Tạo file trong `app/controllers/`:
```php
class YourController {
    public function index() {
        // Your code here
    }
}
```

## Lưu Ý Bảo Mật

1. **Đổi mật khẩu mặc định** sau khi cài đặt
2. **Không commit** file `env.php` chứa thông tin nhạy cảm
3. **Validate input** từ người dùng
4. **Sử dụng prepared statements** (đã được áp dụng trong Models)
5. **Bảo vệ routes** cần xác thực

## Hỗ Trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
- Logs của web server (Apache/Nginx)
- PHP error logs
- Database connection settings trong `env.php`
- Quyền truy cập thư mục `public/assets/uploads/`

## License

Dự án này được phát triển cho mục đích học tập và quản lý.

