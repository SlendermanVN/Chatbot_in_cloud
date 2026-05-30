# SportZone Vietnam — Nền tảng thương mại điện tử thể thao

SportZone Vietnam là website bán hàng thể thao trực tuyến được xây dựng bằng PHP thuần theo kiến trúc MVC (Model-View-Controller), không sử dụng framework. Hệ thống hỗ trợ đầy đủ luồng mua sắm từ duyệt sản phẩm, giỏ hàng, thanh toán đến quản lý đơn hàng, cùng bảng điều khiển admin toàn diện.

---

## Mục lục

- [Giới thiệu dự án](#giới-thiệu-dự-án)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Hướng dẫn cài đặt](#hướng-dẫn-cài-đặt)
- [Docker & Swarm](#docker--swarm)
- [Tài khoản mặc định](#tài-khoản-mặc-định)
- [Tính năng chính](#tính-năng-chính)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Bảo mật](#bảo-mật)

---

## Giới thiệu dự án

**SportZone Vietnam** được xây dựng nhằm cung cấp nền tảng mua sắm trực tuyến chuyên biệt cho các sản phẩm thể thao như giày, quần áo, phụ kiện và thiết bị tập luyện. Hệ thống hướng đến hai nhóm người dùng chính:

- **Khách hàng**: duyệt sản phẩm, quản lý giỏ hàng, đặt hàng, theo dõi đơn hàng và viết đánh giá sản phẩm.
- **Quản trị viên**: quản lý toàn bộ nội dung website — sản phẩm, đơn hàng, người dùng, tin tức, FAQ và cấu hình hệ thống.

**Mục tiêu**:

- Xây dựng hệ thống thương mại điện tử hoàn chỉnh theo mô hình MVC thuần PHP
- Áp dụng các kỹ thuật bảo mật: CSRF, bcrypt, prepared statements, phân quyền
- Giao diện người dùng thân thiện, responsive trên đa thiết bị
- Quản lý sản phẩm linh hoạt với ảnh đa dạng, giá khuyến mãi, phân loại danh mục

**Phạm vi**:

- Website bán lẻ hàng thể thao với đầy đủ luồng mua sắm
- Hệ thống đánh giá sản phẩm có kiểm duyệt
- Bảng tin tức/blog thể thao kèm bình luận
- Trang FAQ và liên hệ với quản lý phản hồi
- Dashboard admin quản lý toàn bộ hệ thống

---

## Công nghệ sử dụng

| Thành phần             | Công nghệ                             |
| ---------------------- | ------------------------------------- |
| Ngôn ngữ backend       | PHP 7.4+ (thuần, không framework)     |
| Kiến trúc              | MVC (Model - View - Controller)       |
| Cơ sở dữ liệu          | MySQL / MariaDB                       |
| Kết nối DB             | PDO với Prepared Statements           |
| Frontend (trang khách) | HTML5, CSS3, Tailwind CSS, JavaScript |
| Frontend (admin)       | Bootstrap 4, Srtdash Admin Template   |
| Icon                   | Font Awesome 6                        |
| Máy chủ                | Apache (XAMPP)                        |
| Upload ảnh             | PHP `finfo` MIME validation           |
| Session                | PHP Native Sessions                   |
| Bảo mật                | CSRF token, bcrypt password hashing   |

---

## Yêu cầu hệ thống

- **PHP**: 7.4 trở lên (khuyến nghị PHP 8.x)
- **MySQL**: 5.7+ hoặc MariaDB 10.3+
- **Apache**: 2.4+ (có mod_rewrite)
- **XAMPP**: 8.x (hoặc tương đương: WAMP, Laragon)
- **Dung lượng**: tối thiểu 100MB trống
- **RAM**: 512MB trở lên

---

## Hướng dẫn cài đặt

### Bước 1 — Tải mã nguồn

```bash
# Clone repo vào thư mục htdocs của XAMPP
git clone <repo-url> D:/1/XAMPP/htdocs/main-repo
```

Hoặc tải ZIP và giải nén vào `C:/xampp/htdocs/main-repo` (Windows).

### Bước 2 — Khởi động XAMPP

1. Mở **XAMPP Control Panel**
2. Start **Apache** và **MySQL**
3. Đảm bảo cổng 80 (Apache) và 3306 (MySQL) không bị chiếm dụng

### Bước 3 — Tạo cơ sở dữ liệu

1. Truy cập `http://localhost/phpmyadmin`
2. Tạo database mới: `sportzone_db` (charset: `utf8mb4_unicode_ci`)
3. Chọn database vừa tạo → tab **Import**
4. Import file schema: `database/sportzone_db.sql`
5. Import file dữ liệu mẫu: `database/sportzone_sample_data.sql`

### Bước 4 — Cấu hình kết nối

Mở file `app/config/database.php` và điều chỉnh thông tin kết nối:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sportzone_db');
define('DB_USER', 'root');       // tên user MySQL
define('DB_PASS', '');           // mật khẩu MySQL (XAMPP mặc định để trống)
```

### Bước 5 — Cấu hình BASE_URL

Mở `public/index.php` và kiểm tra:

```php
define('BASE_URL', '/main-repo/public');
```

Thay đổi nếu dự án được đặt ở thư mục khác.

### Bước 6 — Phân quyền thư mục upload

Đảm bảo thư mục sau có quyền ghi (Apache cần write access):

```
public/uploads/
public/uploads/products/
public/uploads/news/
public/uploads/avatars/
```

### Bước 7 — Truy cập ứng dụng

| Trang       | URL                                                                |
| ----------- | ------------------------------------------------------------------ |
| Trang khách | `http://localhost/main-repo/public/`                               |
| Trang admin | `http://localhost/main-repo/public/index.php?route=admin_products` |
| phpMyAdmin  | `http://localhost/phpmyadmin`                                      |

---

## Docker & Swarm

Nếu bạn muốn chạy site bằng Docker Swarm, xem hướng dẫn đầy đủ tại [README-DOCKER.md](README-DOCKER.md).

Tài liệu này bao gồm:

- build image PHP/Apache
- deploy `docker-stack.yml`
- Grafana + Prometheus + cAdvisor
- script autoscale cho service `web`

---

## Tài khoản mặc định

| Vai trò    | Email / Username | Mật khẩu    |
| ---------- | ---------------- | ----------- |
| Admin      | `admin`          | `Admin@123` |
| Khách hàng | `user1`          | `User@1234` |

> Đổi mật khẩu ngay sau lần đăng nhập đầu tiên.

---

## Tính năng chính

### Phía khách hàng (Frontend)

#### Xác thực & Tài khoản

- **Đăng ký tài khoản**: nhập họ tên, username, email, mật khẩu (yêu cầu tối thiểu 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt)
- **Đăng nhập / Đăng xuất**: xác thực session, phân quyền tự động theo role (`admin` / `customer`)
- **Quản lý hồ sơ**: cập nhật họ tên, username, email; upload ảnh đại diện (hỗ trợ JPG/PNG/WEBP)
- **Đổi mật khẩu**: xác minh mật khẩu hiện tại trước khi cho phép đổi

#### Sản phẩm & Danh mục

- **Danh sách sản phẩm**: hiển thị dạng lưới, phân trang 12 sản phẩm/trang, sắp xếp theo nổi bật và mới nhất
- **Tìm kiếm full-text**: tìm theo tên và mô tả sản phẩm (MySQL FULLTEXT BOOLEAN MODE)
- **Lọc theo danh mục**: chọn nhiều danh mục cùng lúc để lọc kết quả
- **Chi tiết sản phẩm**:
  - Gallery ảnh (nhiều ảnh, ảnh chính hiển thị to, ảnh phụ dạng thumbnail)
  - Giá gốc và giá khuyến mãi (hiển thị % giảm giá)
  - Tình trạng tồn kho (còn hàng / hết hàng / sắp hết)
  - Mô tả chi tiết
  - Danh mục sản phẩm
  - Sản phẩm liên quan (cùng danh mục)
  - Đánh giá từ khách hàng đã mua
- **Sản phẩm nổi bật**: hiển thị trên trang chủ (section featured products)

#### Giỏ hàng

- **Thêm vào giỏ**: nút "Thêm vào giỏ" trên trang danh sách và trang chi tiết sản phẩm
- **Xem giỏ hàng**: danh sách sản phẩm, số lượng, đơn giá, thành tiền từng mặt hàng
- **Cập nhật số lượng**: tăng/giảm số lượng trực tiếp, tổng tiền cập nhật tức thì qua AJAX
- **Xóa sản phẩm**: xóa từng mặt hàng khỏi giỏ
- **Hiển thị số lượng giỏ**: badge số lượng sản phẩm trên icon giỏ hàng ở header (cập nhật real-time)
- **Kiểm tra tồn kho**: không cho thêm vào giỏ khi sản phẩm hết hàng

#### Thanh toán & Đặt hàng

- **Trang checkout**:
  - Xem lại danh sách sản phẩm trước khi đặt
  - Nhập thông tin giao hàng: họ tên, số điện thoại, địa chỉ đầy đủ
  - Thêm ghi chú đơn hàng (tuỳ chọn)
- **Xác nhận đặt hàng**: kiểm tra tồn kho lần cuối trước khi tạo đơn
- **Trang xác nhận thành công**: hiển thị mã đơn hàng và thông tin tóm tắt sau khi đặt thành công
- **Trừ tồn kho tự động**: stock sản phẩm được trừ ngay khi đơn hàng được giao thành công

#### Quản lý đơn hàng (Khách)

- **Lịch sử đơn hàng**: danh sách tất cả đơn đã đặt, phân trang, kèm trạng thái
- **Chi tiết đơn hàng**:
  - Danh sách sản phẩm trong đơn, số lượng, đơn giá
  - Địa chỉ giao hàng và thông tin liên hệ
  - Trạng thái đơn hàng (pending → processing → shipped → delivered / cancelled)
  - Thời điểm đặt hàng và cập nhật lần cuối

#### Đánh giá sản phẩm

- **Gửi đánh giá**: chọn số sao (1–5) và viết nhận xét, chỉ dành cho khách đã đăng nhập
- **Hiển thị đánh giá**: danh sách review đã được duyệt trên trang chi tiết sản phẩm
- **Điểm trung bình**: hiển thị rating trung bình kèm số lượt đánh giá

#### Tin tức & Blog

- **Danh sách tin tức**: bài viết theo thể thao, phân trang
- **Chi tiết bài viết**: nội dung đầy đủ, ngày đăng, ảnh thumbnail
- **Bình luận bài viết**: gửi bình luận (cần đăng nhập), hiển thị sau khi admin duyệt

#### FAQ & Hỗ trợ

- **Duyệt câu hỏi**: danh sách FAQ nhóm theo chủ đề, tìm kiếm theo từ khóa
- **Gửi câu hỏi mới**: form gửi câu hỏi trực tiếp từ trang FAQ
- **Trang liên hệ**: form liên hệ (tên, email, nội dung), thông tin liên hệ (điện thoại, email, Zalo)

---

### Phía quản trị (Admin Dashboard)

#### Quản lý sản phẩm

- **Danh sách sản phẩm**: bảng tổng hợp kèm ảnh thumbnail, SKU, giá, tồn kho, trạng thái; tìm kiếm theo tên/SKU; phân trang
- **Thêm sản phẩm mới**:
  - Tên, slug (tự động tạo từ tên), SKU, mô tả
  - Giá gốc và giá khuyến mãi (validate sale_price < price)
  - Tồn kho ban đầu
  - Chọn danh mục
  - Upload nhiều ảnh cùng lúc (JPG/PNG/WEBP/GIF, tối đa 5MB/ảnh)
  - Ảnh đầu tiên upload tự động là ảnh chính (primary)
  - SEO: meta title, meta description
  - Bật/tắt hiển thị, đánh dấu nổi bật
- **Chỉnh sửa sản phẩm**: cập nhật mọi thông tin; quản lý ảnh riêng biệt (upload thêm, xóa ảnh cũ, đổi ảnh chính)
- **Ẩn sản phẩm (Soft Delete)**: ẩn sản phẩm thay vì xóa hẳn để bảo toàn dữ liệu đơn hàng
- **Quản lý ảnh sản phẩm**:
  - Xem toàn bộ ảnh hiện tại dạng lưới
  - Xóa từng ảnh
  - Đặt ảnh làm ảnh chính (primary)

#### Quản lý đơn hàng

- **Danh sách đơn hàng**: tất cả đơn kèm thông tin khách, tổng tiền, trạng thái; lọc theo trạng thái; tìm kiếm theo keyword
- **Thống kê nhanh**: số đơn pending, processing, shipped, delivered, cancelled
- **Cập nhật trạng thái**: chuyển đổi trạng thái đơn hàng qua các bước; tự động cộng lại stock khi hủy đơn (cancelled) hoặc trừ stock khi giao thành công (delivered)

#### Quản lý người dùng

- **Danh sách tài khoản**: bảng user kèm email, ngày tham gia, trạng thái; tìm kiếm theo keyword
- **Khóa / Mở khóa tài khoản**: ban/unban user (không xóa dữ liệu)
- **Reset mật khẩu**: tạo mật khẩu tạm thời cho user

#### Quản lý tin tức

- **Danh sách bài viết**: tiêu đề, thumbnail, trạng thái publish; tìm kiếm theo tiêu đề
- **Tạo/Chỉnh sửa bài viết**: tiêu đề, slug, nội dung, ảnh thumbnail, trạng thái đăng (public/draft), SEO metadata
- **Xóa bài viết**: xóa bài viết và dữ liệu liên quan

#### Kiểm duyệt bình luận & Đánh giá

- **Danh sách chờ duyệt**: hiển thị cả bình luận bài viết và đánh giá sản phẩm chưa được duyệt
- **Duyệt/Từ chối**: approve để công khai, hoặc xóa bình luận/đánh giá không phù hợp
- **Badge thông báo**: số lượng pending hiển thị trên menu sidebar

#### Quản lý FAQ

- **CRUD đầy đủ**: thêm, sửa, xóa câu hỏi; phân nhóm theo danh mục; sắp xếp thứ tự hiển thị
- **Bật/Tắt FAQ**: kiểm soát visibility từng câu hỏi mà không cần xóa

#### Quản lý liên hệ

- **Hộp thư liên hệ**: toàn bộ form liên hệ từ khách; đánh dấu đã đọc; badge unread trên menu
- **Chi tiết liên hệ**: xem nội dung đầy đủ, thông tin người gửi
- **Trả lời / Xóa**: gửi email phản hồi hoặc xóa tin nhắn đã xử lý

#### Cài đặt hệ thống

- **Thông tin website**: tên site, logo, tagline, địa chỉ, số điện thoại, email liên hệ
- **Cài đặt kinh doanh**: bật/tắt toàn bộ site, cho phép đặt hàng khi hết hàng (backorder)
- **Lưu tức thì**: mọi thay đổi có hiệu lực ngay sau khi save

---

## Cấu trúc thư mục

```
main-repo/
├── app/
│   ├── Controllers/       # Xử lý logic (ProductController, OrderController, ...)
│   ├── Models/            # Truy vấn CSDL (Product, Order, User, ...)
│   ├── Views/
│   │   ├── frontend/      # Giao diện trang khách (Tailwind CSS)
│   │   ├── admin/         # Giao diện admin (Bootstrap 4 / Srtdash)
│   │   └── templates/     # Header, footer dùng chung
│   └── config/            # Cấu hình DB, hằng số
├── database/
│   ├── sportzone_db.sql           # Schema (tables, views, indexes)
│   └── sportzone_sample_data.sql  # Dữ liệu mẫu
├── public/
│   ├── index.php          # Front Controller (điểm vào duy nhất)
│   ├── assets/
│   │   ├── css/           # CSS tùy chỉnh
│   │   └── js/            # JavaScript
│   └── uploads/
│       ├── products/      # Ảnh sản phẩm do admin upload
│       ├── news/          # Ảnh thumbnail bài viết
│       └── avatars/       # Ảnh đại diện người dùng
└── README.md
```

---

## Bảo mật

| Biện pháp               | Mô tả                                                                                |
| ----------------------- | ------------------------------------------------------------------------------------ |
| **CSRF Protection**     | Token ngẫu nhiên nhúng trong mọi form POST; server verify trước khi xử lý            |
| **Password Hashing**    | `password_hash()` với `PASSWORD_BCRYPT`; `password_verify()` khi đăng nhập           |
| **Prepared Statements** | Toàn bộ truy vấn SQL dùng PDO Prepared Statements, tránh SQL Injection               |
| **RBAC**                | Phân quyền rõ ràng: guest / customer / admin; admin routes kiểm tra role mỗi request |
| **File Upload**         | Kiểm tra MIME type thực (qua `finfo`), giới hạn extension, giới hạn kích thước 5MB   |
| **XSS Prevention**      | `htmlspecialchars()` cho mọi dữ liệu xuất ra HTML                                    |
| **Session Security**    | Session-based auth; session destroy hoàn toàn khi logout                             |
| **Password Policy**     | Yêu cầu tối thiểu 8 ký tự gồm chữ hoa, chữ thường, số và ký tự đặc biệt              |
