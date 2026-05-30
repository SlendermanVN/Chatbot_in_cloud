-- =============================================================
--  DATABASE : sportzone_db
--  PROJECT  : SportZone Vietnam - Website Dụng cụ Thể thao
--  SUBJECT  : Lập trình Web (HK2 2025-2026)
--  ENGINE   : InnoDB   (FK, transaction)
--  CHARSET  : utf8mb4  (tiếng Việt + emoji)
--  NORM     : 3NF
--  NOTE     : File này chỉ chứa SCHEMA + TRIGGERS + VIEWS thiết yếu.
--             Dữ liệu mẫu (INSERT) nằm ở file: sportzone_sample_data.sql
-- =============================================================

DROP DATABASE IF EXISTS sportzone_db;
CREATE DATABASE sportzone_db
    CHARACTER SET utf8mb4
    COLLATE       utf8mb4_unicode_ci;
USE sportzone_db;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- =============================================================
-- 1. USERS
-- =============================================================
CREATE TABLE users (
    id             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username       VARCHAR(50)     NOT NULL,
    email          VARCHAR(100)    NOT NULL,
    password_hash  VARCHAR(255)    NOT NULL
                       COMMENT 'Kết quả password_hash() PHP - KHÔNG lưu plain text',
    full_name      VARCHAR(100)    DEFAULT NULL,
    phone          VARCHAR(20)     DEFAULT NULL,
    address        TEXT            DEFAULT NULL,
    avatar         VARCHAR(255)    NOT NULL DEFAULT 'uploads/avatars/default.png'
                       COMMENT 'Đường dẫn file nội bộ, không dùng URL ngoài',
    role           ENUM('member','admin')
                                   NOT NULL DEFAULT 'member',
    is_banned      TINYINT(1)      NOT NULL DEFAULT 0
                       COMMENT '0 = hoạt động, 1 = bị khóa',
    reset_token    VARCHAR(100)    DEFAULT NULL,
    reset_expires  DATETIME        DEFAULT NULL,
    created_at     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_email    (email),
    UNIQUE KEY uk_username (username),
    KEY        idx_role    (role),
    KEY        idx_banned  (is_banned)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Người dùng hệ thống - member và admin';

-- =============================================================
-- 2. CATEGORIES  (danh mục sản phẩm đa cấp)
-- =============================================================
CREATE TABLE categories (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    slug        VARCHAR(120)  NOT NULL,
    parent_id   INT UNSIGNED  DEFAULT NULL
                    COMMENT 'NULL = danh mục gốc, FK tự tham chiếu',
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY  uk_slug    (slug),
    KEY         idx_parent (parent_id),
    CONSTRAINT  fk_cat_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh mục sản phẩm thể thao đa cấp';
-- LƯU Ý: Chặn category tự tham chiếu (parent_id = id) được xử lý ở tầng PHP
-- vì CHECK constraint tham chiếu cột khác trong cùng row không tương thích
-- MariaDB 10.x. MySQL 8+ hỗ trợ nhưng để đồng bộ môi trường demo, kiểm tra
-- ở Controller khi thêm/sửa category.

-- =============================================================
-- 3. PRODUCTS
-- =============================================================
CREATE TABLE products (
    id               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    category_id      INT UNSIGNED    DEFAULT NULL,
    name             VARCHAR(200)    NOT NULL,
    slug             VARCHAR(220)    NOT NULL,
    description      TEXT            DEFAULT NULL,
    price            DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    sale_price       DECIMAL(15,2)   DEFAULT NULL
                         COMMENT 'Giá KM - NULL nếu không KM, phải < price',
    stock            INT             NOT NULL DEFAULT 0,
    sku              VARCHAR(50)     DEFAULT NULL,
    is_active        TINYINT(1)      NOT NULL DEFAULT 1,
    is_featured      TINYINT(1)      NOT NULL DEFAULT 0,
    meta_title       VARCHAR(200)    DEFAULT NULL,
    meta_description VARCHAR(300)    DEFAULT NULL,
    created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                              ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    UNIQUE KEY   uk_slug        (slug),
    KEY          idx_category   (category_id),
    KEY          idx_active     (is_active),
    KEY          idx_featured   (is_featured),
    KEY          idx_price      (price),
    FULLTEXT KEY ft_search      (name, description),
    CONSTRAINT   fk_prod_cat
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT   chk_prod_price
        CHECK (price > 0),
    CONSTRAINT   chk_prod_stock
        CHECK (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sản phẩm dụng cụ thể thao';
-- LƯU Ý: Ràng buộc sale_price < price được enforce bằng trigger
-- trg_product_before_update (xem phần TRIGGERS) vì MariaDB 10.x không
-- hỗ trợ CHECK tham chiếu cột khác trong cùng row.

-- =============================================================
-- 4. PRODUCT_IMAGES  (thực thể yếu của PRODUCT)
-- =============================================================
CREATE TABLE product_images (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    product_id  INT UNSIGNED  NOT NULL,
    image_path  VARCHAR(255)  NOT NULL
                    COMMENT 'uploads/products/ten-file.jpg - lưu nội bộ',
    alt_text    VARCHAR(200)  DEFAULT NULL,
    is_primary  TINYINT(1)    NOT NULL DEFAULT 0,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY         idx_product (product_id),
    KEY         idx_primary (product_id, is_primary),
    CONSTRAINT  fk_img_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Hình ảnh sản phẩm - 1 sản phẩm nhiều ảnh';

-- =============================================================
-- 5. CART_ITEMS  (giỏ hàng lưu DB)
-- =============================================================
CREATE TABLE cart_items (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NOT NULL,
    product_id  INT UNSIGNED  NOT NULL,
    quantity    INT           NOT NULL DEFAULT 1,
    added_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    UNIQUE KEY   uk_user_product (user_id, product_id),
    KEY          idx_user        (user_id),
    KEY          idx_product     (product_id),
    CONSTRAINT   fk_cart_user
        FOREIGN KEY (user_id)    REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT   fk_cart_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT   chk_cart_qty
        CHECK (quantity >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Giỏ hàng - lưu DB thay vì session';

-- =============================================================
-- 6. CUSTOMER_ORDERS
-- =============================================================
CREATE TABLE customer_orders (
    id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    user_id          INT UNSIGNED   DEFAULT NULL
                         COMMENT 'NULL = khách vãng lai',
    recipient_name   VARCHAR(100)   NOT NULL,
    recipient_phone  VARCHAR(20)    NOT NULL,
    shipping_address TEXT           NOT NULL,
    total_amount     DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
    status           ENUM(
                         'pending',
                         'processing',
                         'shipped',
                         'delivered',
                         'cancelled'
                     ) NOT NULL DEFAULT 'pending',
    note             TEXT           DEFAULT NULL,
    created_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                             ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY          idx_user    (user_id),
    KEY          idx_status  (status),
    KEY          idx_created (created_at),
    CONSTRAINT   fk_order_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT   chk_order_total
        CHECK (total_amount >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Đơn hàng của khách';

-- =============================================================
-- 7. ORDER_ITEMS  (thực thể yếu của CUSTOMER_ORDERS)
-- =============================================================
CREATE TABLE order_items (
    id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    order_id        INT UNSIGNED   NOT NULL,
    product_id      INT UNSIGNED   DEFAULT NULL,
    product_name    VARCHAR(200)   NOT NULL
                        COMMENT 'Snapshot tên SP tại thời điểm đặt',
    price_at_order  DECIMAL(15,2)  NOT NULL
                        COMMENT 'Snapshot giá tại thời điểm đặt',
    quantity        INT            NOT NULL DEFAULT 1,
    PRIMARY KEY  (id),
    KEY          idx_order   (order_id),
    KEY          idx_product (product_id),
    CONSTRAINT   fk_oi_order
        FOREIGN KEY (order_id)   REFERENCES customer_orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT   fk_oi_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT   chk_oi_qty
        CHECK (quantity >= 1),
    CONSTRAINT   chk_oi_price
        CHECK (price_at_order >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Chi tiết đơn hàng - snapshot giá và tên sản phẩm';

-- =============================================================
-- 8. NEWS_CATEGORIES
-- =============================================================
CREATE TABLE news_categories (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    slug        VARCHAR(120)  NOT NULL,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY  uk_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh mục bài viết / tin tức thể thao';

-- =============================================================
-- 9. ARTICLES  (bài viết / tin tức)
-- =============================================================
CREATE TABLE articles (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    author_id        INT UNSIGNED  DEFAULT NULL,
    category_id      INT UNSIGNED  DEFAULT NULL,
    title            VARCHAR(250)  NOT NULL,
    slug             VARCHAR(270)  NOT NULL,
    summary          TEXT          DEFAULT NULL,
    content          LONGTEXT      NOT NULL,
    thumbnail        VARCHAR(255)  DEFAULT NULL,
    meta_title       VARCHAR(200)  DEFAULT NULL,
    meta_description VARCHAR(300)  DEFAULT NULL,
    meta_keywords    VARCHAR(300)  DEFAULT NULL,
    is_published     TINYINT(1)    NOT NULL DEFAULT 0,
    is_featured      TINYINT(1)    NOT NULL DEFAULT 0,
    views            INT UNSIGNED  NOT NULL DEFAULT 0,
    published_at     DATETIME      DEFAULT NULL,
    created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    UNIQUE KEY   uk_slug       (slug),
    KEY          idx_author    (author_id),
    KEY          idx_category  (category_id),
    KEY          idx_published (is_published, published_at),
    FULLTEXT KEY ft_search     (title, summary, content),
    CONSTRAINT   fk_art_author
        FOREIGN KEY (author_id)   REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT   fk_art_category
        FOREIGN KEY (category_id) REFERENCES news_categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bài viết / tin tức thể thao - đầy đủ trường SEO';

-- =============================================================
-- 10. REVIEWS  (bình luận dùng chung SP & bài viết)
-- =============================================================
CREATE TABLE reviews (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NOT NULL,
    product_id  INT UNSIGNED  DEFAULT NULL,
    article_id  INT UNSIGNED  DEFAULT NULL,
    content     TEXT          NOT NULL,
    rating      TINYINT       DEFAULT NULL
                    COMMENT '1-5 sao, chỉ dùng cho sản phẩm',
    is_approved TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY          idx_user     (user_id),
    KEY          idx_product  (product_id),
    KEY          idx_article  (article_id),
    KEY          idx_approved (is_approved),
    CONSTRAINT   fk_rev_user
        FOREIGN KEY (user_id)    REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT   fk_rev_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT   fk_rev_article
        FOREIGN KEY (article_id) REFERENCES articles(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT   chk_rev_rating
        CHECK (rating IS NULL OR (rating >= 1 AND rating <= 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bình luận / đánh giá - dùng chung cho sản phẩm và bài viết';
-- LƯU Ý: Ràng buộc "review phải thuộc đúng 1 đối tượng (product HOẶC article)"
-- được enforce bằng trigger trg_review_before_insert / update (xem phần TRIGGERS).

-- =============================================================
-- 11. CONTACTS
-- =============================================================
CREATE TABLE contacts (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  DEFAULT NULL,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL,
    phone       VARCHAR(20)   DEFAULT NULL,
    subject     VARCHAR(200)  DEFAULT NULL,
    message     TEXT          NOT NULL,
    status      ENUM('unread','read','replied')
                              NOT NULL DEFAULT 'unread',
    admin_note  TEXT          DEFAULT NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY         idx_status  (status),
    KEY         idx_user    (user_id),
    KEY         idx_created (created_at),
    CONSTRAINT  fk_contact_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tin nhắn liên hệ / tư vấn từ khách hàng';

-- =============================================================
-- 12. FAQ_CATEGORIES
-- =============================================================
CREATE TABLE faq_categories (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh mục câu hỏi thường gặp';

-- =============================================================
-- 13. FAQS
-- =============================================================
CREATE TABLE faqs (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED  DEFAULT NULL,
    question    TEXT          NOT NULL,
    answer      LONGTEXT      NOT NULL,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                       ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY         idx_category (category_id),
    KEY         idx_active   (is_active),
    FULLTEXT KEY ft_search   (question, answer),
    CONSTRAINT  fk_faq_cat
        FOREIGN KEY (category_id) REFERENCES faq_categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Câu hỏi thường gặp về dụng cụ thể thao';

-- =============================================================
-- 14. SITE_SETTINGS  (cài đặt key-value)
-- =============================================================
CREATE TABLE site_settings (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    setting_key   VARCHAR(100)  NOT NULL,
    setting_value LONGTEXT      DEFAULT NULL,
    group_name    VARCHAR(50)   NOT NULL DEFAULT 'general',
    label         VARCHAR(150)  DEFAULT NULL,
    input_type    VARCHAR(30)   NOT NULL DEFAULT 'text',
    PRIMARY KEY (id),
    UNIQUE KEY  uk_key    (setting_key),
    KEY         idx_group (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cài đặt hệ thống dạng key-value';

-- Dữ liệu mặc định cho site_settings (seed toàn bộ keys cần thiết)
INSERT INTO site_settings (setting_key, setting_value, group_name, label, input_type) VALUES
-- Thông tin website
('site_name',              'SportZone Vietnam',                                                         'general', 'Tên website',                    'text'),
('site_tagline',           'Dụng cụ thể thao chính hãng - Giá tốt',                                   'general', 'Slogan',                          'text'),
('site_logo',              'uploads/settings/logo.png',                                                'general', 'Logo',                            'image'),
('site_favicon',           'uploads/settings/favicon.ico',                                             'general', 'Favicon',                         'image'),
('meta_description',       'SportZone - Shop dụng cụ thể thao chính hãng, đa dạng sản phẩm Nike, Adidas, Yonex, Wilson.', 'seo', 'Meta description', 'textarea'),
('meta_keywords',          'dụng cụ thể thao, giày bóng đá, vợt cầu lông, bóng rổ',                  'seo',     'Meta keywords',                   'text'),
-- Thông tin liên hệ
('contact_phone',          '1900-6969',                                                                'contact', 'Hotline',                         'text'),
('contact_email',          'support@sportzone.vn',                                                     'contact', 'Email hỗ trợ',                    'email'),
('contact_address',        '123 Lý Tự Trọng, Q.1, TP.HCM',                                            'contact', 'Địa chỉ công ty',                 'text'),
('store_address',          '123 Lý Tự Trọng, Q.1, TP.HCM',                                            'contact', 'Địa chỉ cửa hàng',                'text'),
-- Mạng xã hội
('social_facebook',        'https://facebook.com/sportzone.vn',                                        'social',  'Facebook URL',                    'url'),
('social_instagram',       'https://instagram.com/sportzone.vn',                                       'social',  'Instagram URL',                   'url'),
('social_youtube',         'https://youtube.com/@sportzone',                                           'social',  'YouTube URL',                     'url'),
('facebook_url',           'https://facebook.com/sportzone.vn',                                        'social',  'Facebook',                        'url'),
('youtube_url',            'https://youtube.com/@sportzone',                                           'social',  'YouTube',                         'url'),
-- Cài đặt cửa hàng
('currency',               'VND',                                                                       'store',   'Đơn vị tiền tệ',                 'text'),
('shipping_fee',           '30000',                                                                     'store',   'Phí vận chuyển cố định (VNĐ)',   'number'),
('free_shipping_threshold','500000',                                                                    'store',   'Miễn phí ship từ (VNĐ)',         'number'),
('allow_backorder',        '0',                                                                         'store',   'Cho phép đặt khi hết hàng',      'checkbox'),
('site_active',            '1',                                                                         'store',   'Website đang hoạt động',         'checkbox'),
-- Nội dung trang Giới thiệu
('about_title',            'Về SportZone Vietnam',                                                      'about',   'Tiêu đề trang giới thiệu',       'text'),
('about_description',      'Chúng tôi không chỉ bán sản phẩm thể thao, chúng tôi cung cấp giải pháp để bạn chinh phục mọi giới hạn.', 'about', 'Mô tả ngắn',    'textarea'),
('about_mission',          'Mang đến những thiết bị thể thao chất lượng nhất để truyền cảm hứng cho lối sống lành mạnh và năng động.', 'about', 'Sứ mệnh',        'textarea'),
('about_vision',           'Trở thành điểm đến số 1 cho cộng đồng yêu thể thao tại Việt Nam với trải nghiệm mua sắm vượt trội.',      'about', 'Tầm nhìn',        'textarea'),
('about_values',           'Chất lượng hàng đầu - Phục vụ tận tâm - Sáng tạo không ngừng - Trách nhiệm cộng đồng.',                  'about', 'Giá trị cốt lõi', 'textarea'),
('about_stat_customers',   '10k+',                                                                                                      'about', 'Số khách hàng',   'text'),
('about_stat_products',    '500+',                                                                                                       'about', 'Số sản phẩm',     'text'),
('about_stat_brands',      '15+',                                                                                                        'about', 'Số thương hiệu',  'text'),
('about_stat_support',     '24/7',                                                                                                       'about', 'Hỗ trợ',          'text');


-- =============================================================
-- 15. PAGES  (trang tĩnh)
-- =============================================================
CREATE TABLE pages (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    page_key         VARCHAR(80)   NOT NULL,
    title            VARCHAR(200)  NOT NULL,
    content          LONGTEXT      NOT NULL,
    meta_title       VARCHAR(200)  DEFAULT NULL,
    meta_description VARCHAR(300)  DEFAULT NULL,
    updated_by       INT UNSIGNED  DEFAULT NULL,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY  uk_page_key (page_key),
    CONSTRAINT  fk_page_editor
        FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Nội dung trang tĩnh - Giới thiệu, Chính sách...';

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- STORED FUNCTIONS  (chỉ giữ các hàm thực sự được dùng ở views)
-- =============================================================
DELIMITER $$

-- Giá hiệu lực: sale_price nếu có và > 0, không thì price
CREATE FUNCTION fn_get_effective_price(p_product_id INT UNSIGNED)
RETURNS DECIMAL(15,2) DETERMINISTIC
BEGIN
    DECLARE v_price      DECIMAL(15,2) DEFAULT 0;
    DECLARE v_sale_price DECIMAL(15,2) DEFAULT NULL;
    SELECT price, sale_price
      INTO v_price, v_sale_price
      FROM products WHERE id = p_product_id;
    RETURN IF(v_sale_price IS NOT NULL AND v_sale_price > 0, v_sale_price, v_price);
END$$

-- Điểm đánh giá trung bình của sản phẩm
CREATE FUNCTION fn_get_avg_rating(p_product_id INT UNSIGNED)
RETURNS DECIMAL(3,1) DETERMINISTIC
BEGIN
    DECLARE v_avg DECIMAL(3,1) DEFAULT 0;
    SELECT COALESCE(ROUND(AVG(rating), 1), 0) INTO v_avg
      FROM reviews
     WHERE product_id  = p_product_id
       AND is_approved = 1
       AND rating IS NOT NULL;
    RETURN v_avg;
END$$

DELIMITER ;

-- =============================================================
-- TRIGGERS  (chỉ giữ các ràng buộc nghiệp vụ cốt lõi)
-- =============================================================
DELIMITER $$

-- -------------------------------------------------------------
-- T1. ORDER_ITEMS -> PRODUCTS.stock
--     Thêm order_item: trừ stock
--     Xóa order_item: hoàn stock
-- -------------------------------------------------------------
CREATE TRIGGER trg_orderitem_after_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    IF NEW.product_id IS NOT NULL THEN
        UPDATE products
           SET stock = stock - NEW.quantity
         WHERE id    = NEW.product_id;
    END IF;
END$$

CREATE TRIGGER trg_orderitem_after_delete
AFTER DELETE ON order_items
FOR EACH ROW
BEGIN
    IF OLD.product_id IS NOT NULL THEN
        UPDATE products
           SET stock = stock + OLD.quantity
         WHERE id    = OLD.product_id;
    END IF;
END$$

-- -------------------------------------------------------------
-- T2. ORDER STATUS -> hoàn / trừ stock khi hủy / khôi phục
-- -------------------------------------------------------------
CREATE TRIGGER trg_order_status_after_update
AFTER UPDATE ON customer_orders
FOR EACH ROW
BEGIN
    -- Chuyển sang cancelled: hoàn stock
    IF NEW.status = 'cancelled' AND OLD.status <> 'cancelled' THEN
        UPDATE products p
         INNER JOIN order_items oi ON oi.product_id = p.id
           SET p.stock = p.stock + oi.quantity
         WHERE oi.order_id    = NEW.id
           AND oi.product_id IS NOT NULL;
    END IF;

    -- Khôi phục từ cancelled: trừ lại stock
    IF OLD.status = 'cancelled' AND NEW.status <> 'cancelled' THEN
        UPDATE products p
         INNER JOIN order_items oi ON oi.product_id = p.id
           SET p.stock = p.stock - oi.quantity
         WHERE oi.order_id    = NEW.id
           AND oi.product_id IS NOT NULL;
    END IF;
END$$

-- -------------------------------------------------------------
-- T3. REVIEWS - ràng buộc nghiệp vụ
--     Review phải thuộc đúng 1 đối tượng: product HOẶC article
--     Rating chỉ hợp lệ cho review sản phẩm
-- -------------------------------------------------------------
CREATE TRIGGER trg_review_before_insert
BEFORE INSERT ON reviews
FOR EACH ROW
BEGIN
    IF NOT (
        (NEW.product_id IS NOT NULL AND NEW.article_id IS NULL)
        OR
        (NEW.product_id IS NULL     AND NEW.article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Review phai thuoc dung 1 doi tuong: product_id hoac article_id.';
    END IF;

    IF NEW.product_id IS NULL AND NEW.rating IS NOT NULL THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Rating chi ap dung cho review san pham.';
    END IF;
END$$

CREATE TRIGGER trg_review_before_update
BEFORE UPDATE ON reviews
FOR EACH ROW
BEGIN
    IF NOT (
        (NEW.product_id IS NOT NULL AND NEW.article_id IS NULL)
        OR
        (NEW.product_id IS NULL     AND NEW.article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Review phai thuoc dung 1 doi tuong: product_id hoac article_id.';
    END IF;
END$$

-- -------------------------------------------------------------
-- T4. PRODUCTS - chặn stock âm khi cập nhật trực tiếp,
--                kiểm tra sale_price < price (ngoài CHECK constraint)
-- -------------------------------------------------------------
CREATE TRIGGER trg_product_before_update
BEFORE UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.stock < 0 THEN
        SET NEW.stock = 0;
    END IF;

    IF NEW.sale_price IS NOT NULL AND NEW.sale_price >= NEW.price THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'sale_price phai nho hon price.';
    END IF;
END$$

DELIMITER ;

-- =============================================================
-- VIEWS  (chỉ các view thực sự dùng trong UI / admin)
-- =============================================================

-- V1. Sản phẩm kèm ảnh chính + tên danh mục + giá hiệu lực + rating
CREATE OR REPLACE VIEW v_products AS
SELECT
    p.id, p.name, p.slug, p.description,
    p.price, p.sale_price,
    fn_get_effective_price(p.id)    AS effective_price,
    p.stock, p.sku,
    p.is_active, p.is_featured,
    p.meta_title, p.meta_description,
    p.created_at,
    c.id                            AS category_id,
    c.name                          AS category_name,
    c.slug                          AS category_slug,
    pi.image_path                   AS primary_image,
    fn_get_avg_rating(p.id)         AS avg_rating
FROM products p
LEFT JOIN categories     c  ON c.id  = p.category_id
LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1;

-- V2. Giỏ hàng đầy đủ thông tin sản phẩm (cho trang /cart)
CREATE OR REPLACE VIEW v_cart_detail AS
SELECT
    ci.id                              AS cart_item_id,
    ci.user_id,
    ci.product_id,
    ci.quantity,
    ci.added_at,
    p.name                             AS product_name,
    p.slug                             AS product_slug,
    p.price,
    p.sale_price,
    fn_get_effective_price(p.id)       AS effective_price,
    fn_get_effective_price(p.id) * ci.quantity AS subtotal,
    p.stock,
    p.is_active,
    pi.image_path                      AS primary_image
FROM cart_items ci
JOIN products   p  ON p.id = ci.product_id
LEFT JOIN product_images pi
       ON pi.product_id = p.id AND pi.is_primary = 1;

-- V3. Chi tiết đơn hàng (dùng cho trang admin Order & trang lịch sử đơn của user)
CREATE OR REPLACE VIEW v_order_details AS
SELECT
    oi.id                            AS item_id,
    oi.order_id,
    oi.product_id,
    oi.product_name,
    oi.price_at_order,
    oi.quantity,
    oi.price_at_order * oi.quantity  AS subtotal,
    co.status                        AS order_status,
    co.created_at                    AS order_date,
    co.user_id,
    co.recipient_name,
    co.recipient_phone,
    co.shipping_address,
    co.total_amount,
    pi.image_path                    AS product_image
FROM order_items      oi
JOIN customer_orders  co ON co.id = oi.order_id
LEFT JOIN product_images pi
       ON pi.product_id = oi.product_id AND pi.is_primary = 1;

-- V4. Contacts chờ xử lý (dashboard admin)
CREATE OR REPLACE VIEW v_pending_contacts AS
SELECT
    c.id, c.name, c.email, c.phone, c.subject, c.message,
    c.status, c.admin_note, c.created_at,
    u.username
FROM contacts c
LEFT JOIN users u ON u.id = c.user_id
WHERE c.status IN ('unread', 'read')
ORDER BY c.created_at DESC;

-- V5. Reviews chờ duyệt (dashboard admin)
CREATE OR REPLACE VIEW v_pending_reviews AS
SELECT
    r.id, r.content, r.rating, r.created_at,
    r.product_id, r.article_id,
    u.username      AS reviewer_name,
    u.email         AS reviewer_email,
    p.name          AS product_name,
    a.title         AS article_title
FROM reviews  r
JOIN users    u ON u.id = r.user_id
LEFT JOIN products  p ON p.id = r.product_id
LEFT JOIN articles  a ON a.id = r.article_id
WHERE r.is_approved = 0
ORDER BY r.created_at ASC;

-- =============================================================
-- DONE - Database sportzone_db đã sẵn sàng
-- Import sample data: sportzone_sample_data.sql
-- =============================================================
