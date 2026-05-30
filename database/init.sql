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
CHARACTER
SET utf8mb4
COLLATE       utf8mb4_unicode_ci;
USE sportzone_db;

SET FOREIGN_KEY_CHECKS
= 0;
SET SQL_MODE
= 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- =============================================================
-- 1. USERS
-- =============================================================
CREATE TABLE users
(
  id INT
  UNSIGNED    NOT NULL AUTO_INCREMENT,
    username       VARCHAR
  (50)     NOT NULL,
    email          VARCHAR
  (100)    NOT NULL,
    password_hash  VARCHAR
  (255)    NOT NULL
                       COMMENT 'Kết quả password_hash() PHP - KHÔNG lưu plain text',
    full_name      VARCHAR
  (100)    DEFAULT NULL,
    phone          VARCHAR
  (20)     DEFAULT NULL,
    address        TEXT            DEFAULT NULL,
    avatar         VARCHAR
  (255)    NOT NULL DEFAULT 'uploads/avatars/default.png'
                       COMMENT 'Đường dẫn file nội bộ, không dùng URL ngoài',
    role           ENUM
  ('member','admin')
                                   NOT NULL DEFAULT 'member',
    is_banned      TINYINT
  (1)      NOT NULL DEFAULT 0
                       COMMENT '0 = hoạt động, 1 = bị khóa',
    reset_token    VARCHAR
  (100)    DEFAULT NULL,
    reset_expires  DATETIME        DEFAULT NULL,
    created_at     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON
  UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY
  (id),
    UNIQUE KEY uk_email
  (email),
    UNIQUE KEY uk_username
  (username),
    KEY        idx_role
  (role),
    KEY        idx_banned
  (is_banned)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Người dùng hệ thống - member và admin';

  -- =============================================================
  -- 2. CATEGORIES  (danh mục sản phẩm đa cấp)
  -- =============================================================
  CREATE TABLE categories
  (
    id INT
    UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR
    (100)  NOT NULL,
    slug        VARCHAR
    (120)  NOT NULL,
    parent_id   INT UNSIGNED  DEFAULT NULL
                    COMMENT 'NULL = danh mục gốc, FK tự tham chiếu',
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT
    (1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY
    (id),
    UNIQUE KEY  uk_slug
    (slug),
    KEY         idx_parent
    (parent_id),
    CONSTRAINT  fk_cat_parent
        FOREIGN KEY
    (parent_id) REFERENCES categories
    (id)
        ON
    DELETE
    SET NULL
    ON
    UPDATE CASCADE
) ENGINE=InnoDB
    DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh mục sản phẩm thể thao đa cấp';
    -- LƯU Ý: Chặn category tự tham chiếu (parent_id = id) được xử lý ở tầng PHP
    -- vì CHECK constraint tham chiếu cột khác trong cùng row không tương thích
    -- MariaDB 10.x. MySQL 8+ hỗ trợ nhưng để đồng bộ môi trường demo, kiểm tra
    -- ở Controller khi thêm/sửa category.

    -- =============================================================
    -- 3. PRODUCTS
    -- =============================================================
    CREATE TABLE products
    (
      id INT
      UNSIGNED    NOT NULL AUTO_INCREMENT,
    category_id      INT UNSIGNED    DEFAULT NULL,
    name             VARCHAR
      (200)    NOT NULL,
    slug             VARCHAR
      (220)    NOT NULL,
    description      TEXT            DEFAULT NULL,
    price            DECIMAL
      (15,2)   NOT NULL DEFAULT 0.00,
    sale_price       DECIMAL
      (15,2)   DEFAULT NULL
                         COMMENT 'Giá KM - NULL nếu không KM, phải < price',
    stock            INT             NOT NULL DEFAULT 0,
    sku              VARCHAR
      (50)     DEFAULT NULL,
    is_active        TINYINT
      (1)      NOT NULL DEFAULT 1,
    is_featured      TINYINT
      (1)      NOT NULL DEFAULT 0,
    meta_title       VARCHAR
      (200)    DEFAULT NULL,
    meta_description VARCHAR
      (300)    DEFAULT NULL,
    created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                              ON
      UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY
      (id),
    UNIQUE KEY   uk_slug
      (slug),
    KEY          idx_category
      (category_id),
    KEY          idx_active
      (is_active),
    KEY          idx_featured
      (is_featured),
    KEY          idx_price
      (price),
    FULLTEXT KEY ft_search
      (name, description),
    CONSTRAINT   fk_prod_cat
        FOREIGN KEY
      (category_id) REFERENCES categories
      (id)
        ON
      DELETE
      SET NULL
      ON
      UPDATE CASCADE,
    CONSTRAINT   chk_prod_price
        CHECK
      (price > 0),
    CONSTRAINT   chk_prod_stock
        CHECK
      (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sản phẩm dụng cụ thể thao';
      -- LƯU Ý: Ràng buộc sale_price < price được enforce bằng trigger
      -- trg_product_before_update (xem phần TRIGGERS) vì MariaDB 10.x không
      -- hỗ trợ CHECK tham chiếu cột khác trong cùng row.

      -- =============================================================
      -- 4. PRODUCT_IMAGES  (thực thể yếu của PRODUCT)
      -- =============================================================
      CREATE TABLE product_images
      (
        id INT
        UNSIGNED  NOT NULL AUTO_INCREMENT,
    product_id  INT UNSIGNED  NOT NULL,
    image_path  VARCHAR
        (255)  NOT NULL
                    COMMENT 'uploads/products/ten-file.jpg - lưu nội bộ',
    alt_text    VARCHAR
        (200)  DEFAULT NULL,
    is_primary  TINYINT
        (1)    NOT NULL DEFAULT 0,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    PRIMARY KEY
        (id),
    KEY         idx_product
        (product_id),
    KEY         idx_primary
        (product_id, is_primary),
    CONSTRAINT  fk_img_product
        FOREIGN KEY
        (product_id) REFERENCES products
        (id)
        ON
        DELETE CASCADE ON
        UPDATE CASCADE
) ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Hình ảnh sản phẩm - 1 sản phẩm nhiều ảnh';

        -- =============================================================
        -- 5. CART_ITEMS  (giỏ hàng lưu DB)
        -- =============================================================
        CREATE TABLE cart_items
        (
          id INT
          UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NOT NULL,
    product_id  INT UNSIGNED  NOT NULL,
    quantity    INT           NOT NULL DEFAULT 1,
    added_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY
          (id),
    UNIQUE KEY   uk_user_product
          (user_id, product_id),
    KEY          idx_user
          (user_id),
    KEY          idx_product
          (product_id),
    CONSTRAINT   fk_cart_user
        FOREIGN KEY
          (user_id)    REFERENCES users
          (id)
        ON
          DELETE CASCADE ON
          UPDATE CASCADE,
    CONSTRAINT   fk_cart_product
        FOREIGN KEY
          (product_id) REFERENCES products
          (id)
        ON
          DELETE CASCADE ON
          UPDATE CASCADE,
    CONSTRAINT   chk_cart_qty
        CHECK
          (quantity >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Giỏ hàng - lưu DB thay vì session';

          -- =============================================================
          -- 6. CUSTOMER_ORDERS
          -- =============================================================
          CREATE TABLE customer_orders
          (
            id INT
            UNSIGNED   NOT NULL AUTO_INCREMENT,
    user_id          INT UNSIGNED   DEFAULT NULL
                         COMMENT 'NULL = khách vãng lai',
    recipient_name   VARCHAR
            (100)   NOT NULL,
    recipient_phone  VARCHAR
            (20)    NOT NULL,
    shipping_address TEXT           NOT NULL,
    total_amount     DECIMAL
            (15,2)  NOT NULL DEFAULT 0.00,
    status           ENUM
            (
                         'pending',
                         'processing',
                         'shipped',
                         'delivered',
                         'cancelled'
                     ) NOT NULL DEFAULT 'pending',
    note             TEXT           DEFAULT NULL,
    created_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                             ON
            UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY
            (id),
    KEY          idx_user
            (user_id),
    KEY          idx_status
            (status),
    KEY          idx_created
            (created_at),
    CONSTRAINT   fk_order_user
        FOREIGN KEY
            (user_id) REFERENCES users
            (id)
        ON
            DELETE
            SET NULL
            ON
            UPDATE CASCADE,
    CONSTRAINT   chk_order_total
        CHECK
            (total_amount >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Đơn hàng của khách';

            -- =============================================================
            -- 7. ORDER_ITEMS  (thực thể yếu của CUSTOMER_ORDERS)
            -- =============================================================
            CREATE TABLE order_items
            (
              id INT
              UNSIGNED   NOT NULL AUTO_INCREMENT,
    order_id        INT UNSIGNED   NOT NULL,
    product_id      INT UNSIGNED   DEFAULT NULL,
    product_name    VARCHAR
              (200)   NOT NULL
                        COMMENT 'Snapshot tên SP tại thời điểm đặt',
    price_at_order  DECIMAL
              (15,2)  NOT NULL
                        COMMENT 'Snapshot giá tại thời điểm đặt',
    quantity        INT            NOT NULL DEFAULT 1,
    PRIMARY KEY
              (id),
    KEY          idx_order
              (order_id),
    KEY          idx_product
              (product_id),
    CONSTRAINT   fk_oi_order
        FOREIGN KEY
              (order_id)   REFERENCES customer_orders
              (id)
        ON
              DELETE CASCADE ON
              UPDATE CASCADE,
    CONSTRAINT   fk_oi_product
        FOREIGN KEY
              (product_id) REFERENCES products
              (id)
        ON
              DELETE
              SET NULL
              ON
              UPDATE CASCADE,
    CONSTRAINT   chk_oi_qty
        CHECK
              (quantity >= 1),
    CONSTRAINT   chk_oi_price
        CHECK
              (price_at_order >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Chi tiết đơn hàng - snapshot giá và tên sản phẩm';

              -- =============================================================
              -- 8. NEWS_CATEGORIES
              -- =============================================================
              CREATE TABLE news_categories
              (
                id INT
                UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR
                (100)  NOT NULL,
    slug        VARCHAR
                (120)  NOT NULL,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT
                (1)    NOT NULL DEFAULT 1,
    PRIMARY KEY
                (id),
    UNIQUE KEY  uk_slug
                (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh mục bài viết / tin tức thể thao';

                -- =============================================================
                -- 9. ARTICLES  (bài viết / tin tức)
                -- =============================================================
                CREATE TABLE articles
                (
                  id INT
                  UNSIGNED  NOT NULL AUTO_INCREMENT,
    author_id        INT UNSIGNED  DEFAULT NULL,
    category_id      INT UNSIGNED  DEFAULT NULL,
    title            VARCHAR
                  (250)  NOT NULL,
    slug             VARCHAR
                  (270)  NOT NULL,
    summary          TEXT          DEFAULT NULL,
    content          LONGTEXT      NOT NULL,
    thumbnail        VARCHAR
                  (255)  DEFAULT NULL,
    meta_title       VARCHAR
                  (200)  DEFAULT NULL,
    meta_description VARCHAR
                  (300)  DEFAULT NULL,
    meta_keywords    VARCHAR
                  (300)  DEFAULT NULL,
    is_published     TINYINT
                  (1)    NOT NULL DEFAULT 0,
    is_featured      TINYINT
                  (1)    NOT NULL DEFAULT 0,
    views            INT UNSIGNED  NOT NULL DEFAULT 0,
    published_at     DATETIME      DEFAULT NULL,
    created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON
                  UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY
                  (id),
    UNIQUE KEY   uk_slug
                  (slug),
    KEY          idx_author
                  (author_id),
    KEY          idx_category
                  (category_id),
    KEY          idx_published
                  (is_published, published_at),
    FULLTEXT KEY ft_search
                  (title, summary, content),
    CONSTRAINT   fk_art_author
        FOREIGN KEY
                  (author_id)   REFERENCES users
                  (id)
        ON
                  DELETE
                  SET NULL
                  ON
                  UPDATE CASCADE,
    CONSTRAINT   fk_art_category
        FOREIGN KEY
                  (category_id) REFERENCES news_categories
                  (id)
        ON
                  DELETE
                  SET NULL
                  ON
                  UPDATE CASCADE
) ENGINE=InnoDB
                  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bài viết / tin tức thể thao - đầy đủ trường SEO';

                  -- =============================================================
                  -- 10. REVIEWS  (bình luận dùng chung SP & bài viết)
                  -- =============================================================
                  CREATE TABLE reviews
                  (
                    id INT
                    UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NOT NULL,
    product_id  INT UNSIGNED  DEFAULT NULL,
    article_id  INT UNSIGNED  DEFAULT NULL,
    content     TEXT          NOT NULL,
    rating      TINYINT       DEFAULT NULL
                    COMMENT '1-5 sao, chỉ dùng cho sản phẩm',
    is_approved TINYINT
                    (1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY
                    (id),
    KEY          idx_user
                    (user_id),
    KEY          idx_product
                    (product_id),
    KEY          idx_article
                    (article_id),
    KEY          idx_approved
                    (is_approved),
    CONSTRAINT   fk_rev_user
        FOREIGN KEY
                    (user_id)    REFERENCES users
                    (id)
        ON
                    DELETE CASCADE ON
                    UPDATE CASCADE,
    CONSTRAINT   fk_rev_product
        FOREIGN KEY
                    (product_id) REFERENCES products
                    (id)
        ON
                    DELETE CASCADE ON
                    UPDATE CASCADE,
    CONSTRAINT   fk_rev_article
        FOREIGN KEY
                    (article_id) REFERENCES articles
                    (id)
        ON
                    DELETE CASCADE ON
                    UPDATE CASCADE,
    CONSTRAINT   chk_rev_rating
        CHECK
                    (rating IS NULL OR
                    (rating >= 1 AND rating <= 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bình luận / đánh giá - dùng chung cho sản phẩm và bài viết';
                    -- LƯU Ý: Ràng buộc "review phải thuộc đúng 1 đối tượng (product HOẶC article)"
                    -- được enforce bằng trigger trg_review_before_insert / update (xem phần TRIGGERS).

                    -- =============================================================
                    -- 11. CONTACTS
                    -- =============================================================
                    CREATE TABLE contacts
                    (
                      id INT
                      UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  DEFAULT NULL,
    name        VARCHAR
                      (100)  NOT NULL,
    email       VARCHAR
                      (100)  NOT NULL,
    phone       VARCHAR
                      (20)   DEFAULT NULL,
    subject     VARCHAR
                      (200)  DEFAULT NULL,
    message     TEXT          NOT NULL,
    status      ENUM
                      ('unread','read','replied')
                              NOT NULL DEFAULT 'unread',
    admin_note  TEXT          DEFAULT NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY
                      (id),
    KEY         idx_status
                      (status),
    KEY         idx_user
                      (user_id),
    KEY         idx_created
                      (created_at),
    CONSTRAINT  fk_contact_user
        FOREIGN KEY
                      (user_id) REFERENCES users
                      (id)
        ON
                      DELETE
                      SET NULL
                      ON
                      UPDATE CASCADE
) ENGINE=InnoDB
                      DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tin nhắn liên hệ / tư vấn từ khách hàng';

                      -- =============================================================
                      -- 12. FAQ_CATEGORIES
                      -- =============================================================
                      CREATE TABLE faq_categories
                      (
                        id INT
                        UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR
                        (100)  NOT NULL,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT
                        (1)    NOT NULL DEFAULT 1,
    PRIMARY KEY
                        (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh mục câu hỏi thường gặp';

                        -- =============================================================
                        -- 13. FAQS
                        -- =============================================================
                        CREATE TABLE faqs
                        (
                          id INT
                          UNSIGNED  NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED  DEFAULT NULL,
    question    TEXT          NOT NULL,
    answer      LONGTEXT      NOT NULL,
    sort_order  SMALLINT      NOT NULL DEFAULT 0,
    is_active   TINYINT
                          (1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                       ON
                          UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY
                          (id),
    KEY         idx_category
                          (category_id),
    KEY         idx_active
                          (is_active),
    FULLTEXT KEY ft_search
                          (question, answer),
    CONSTRAINT  fk_faq_cat
        FOREIGN KEY
                          (category_id) REFERENCES faq_categories
                          (id)
        ON
                          DELETE
                          SET NULL
                          ON
                          UPDATE CASCADE
) ENGINE=InnoDB
                          DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Câu hỏi thường gặp về dụng cụ thể thao';

                          -- =============================================================
                          -- 14. SITE_SETTINGS  (cài đặt key-value)
                          -- =============================================================
                          CREATE TABLE site_settings
                          (
                            id INT
                            UNSIGNED  NOT NULL AUTO_INCREMENT,
    setting_key   VARCHAR
                            (100)  NOT NULL,
    setting_value LONGTEXT      DEFAULT NULL,
    group_name    VARCHAR
                            (50)   NOT NULL DEFAULT 'general',
    label         VARCHAR
                            (150)  DEFAULT NULL,
    input_type    VARCHAR
                            (30)   NOT NULL DEFAULT 'text',
    PRIMARY KEY
                            (id),
    UNIQUE KEY  uk_key
                            (setting_key),
    KEY         idx_group
                            (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Cài đặt hệ thống dạng key-value';

                            -- Dữ liệu mặc định cho site_settings (seed toàn bộ keys cần thiết)
                            INSERT INTO site_settings
                              (setting_key, setting_value, group_name, label, input_type)
                            VALUES
                              -- Thông tin website
                              ('site_name', 'SportZone Vietnam', 'general', 'Tên website', 'text'),
                              ('site_tagline', 'Dụng cụ thể thao chính hãng - Giá tốt', 'general', 'Slogan', 'text'),
                              ('site_logo', 'uploads/settings/logo.png', 'general', 'Logo', 'image'),
                              ('site_favicon', 'uploads/settings/favicon.ico', 'general', 'Favicon', 'image'),
                              ('meta_description', 'SportZone - Shop dụng cụ thể thao chính hãng, đa dạng sản phẩm Nike, Adidas, Yonex, Wilson.', 'seo', 'Meta description', 'textarea'),
                              ('meta_keywords', 'dụng cụ thể thao, giày bóng đá, vợt cầu lông, bóng rổ', 'seo', 'Meta keywords', 'text'),
                              -- Thông tin liên hệ
                              ('contact_phone', '1900-6969', 'contact', 'Hotline', 'text'),
                              ('contact_email', 'support@sportzone.vn', 'contact', 'Email hỗ trợ', 'email'),
                              ('contact_address', '123 Lý Tự Trọng, Q.1, TP.HCM', 'contact', 'Địa chỉ công ty', 'text'),
                              ('store_address', '123 Lý Tự Trọng, Q.1, TP.HCM', 'contact', 'Địa chỉ cửa hàng', 'text'),
                              -- Mạng xã hội
                              ('social_facebook', 'https://facebook.com/sportzone.vn', 'social', 'Facebook URL', 'url'),
                              ('social_instagram', 'https://instagram.com/sportzone.vn', 'social', 'Instagram URL', 'url'),
                              ('social_youtube', 'https://youtube.com/@sportzone', 'social', 'YouTube URL', 'url'),
                              ('facebook_url', 'https://facebook.com/sportzone.vn', 'social', 'Facebook', 'url'),
                              ('youtube_url', 'https://youtube.com/@sportzone', 'social', 'YouTube', 'url'),
                              -- Cài đặt cửa hàng
                              ('currency', 'VND', 'store', 'Đơn vị tiền tệ', 'text'),
                              ('shipping_fee', '30000', 'store', 'Phí vận chuyển cố định (VNĐ)', 'number'),
                              ('free_shipping_threshold', '500000', 'store', 'Miễn phí ship từ (VNĐ)', 'number'),
                              ('allow_backorder', '0', 'store', 'Cho phép đặt khi hết hàng', 'checkbox'),
                              ('site_active', '1', 'store', 'Website đang hoạt động', 'checkbox'),
                              -- Nội dung trang Giới thiệu
                              ('about_title', 'Về SportZone Vietnam', 'about', 'Tiêu đề trang giới thiệu', 'text'),
                              ('about_description', 'Chúng tôi không chỉ bán sản phẩm thể thao, chúng tôi cung cấp giải pháp để bạn chinh phục mọi giới hạn.', 'about', 'Mô tả ngắn', 'textarea'),
                              ('about_mission', 'Mang đến những thiết bị thể thao chất lượng nhất để truyền cảm hứng cho lối sống lành mạnh và năng động.', 'about', 'Sứ mệnh', 'textarea'),
                              ('about_vision', 'Trở thành điểm đến số 1 cho cộng đồng yêu thể thao tại Việt Nam với trải nghiệm mua sắm vượt trội.', 'about', 'Tầm nhìn', 'textarea'),
                              ('about_values', 'Chất lượng hàng đầu - Phục vụ tận tâm - Sáng tạo không ngừng - Trách nhiệm cộng đồng.', 'about', 'Giá trị cốt lõi', 'textarea'),
                              ('about_stat_customers', '10k+', 'about', 'Số khách hàng', 'text'),
                              ('about_stat_products', '500+', 'about', 'Số sản phẩm', 'text'),
                              ('about_stat_brands', '15+', 'about', 'Số thương hiệu', 'text'),
                              ('about_stat_support', '24/7', 'about', 'Hỗ trợ', 'text');


                            -- =============================================================
                            -- 15. PAGES  (trang tĩnh)
                            -- =============================================================
                            CREATE TABLE pages
                            (
                              id INT
                              UNSIGNED  NOT NULL AUTO_INCREMENT,
    page_key         VARCHAR
                              (80)   NOT NULL,
    title            VARCHAR
                              (200)  NOT NULL,
    content          LONGTEXT      NOT NULL,
    meta_title       VARCHAR
                              (200)  DEFAULT NULL,
    meta_description VARCHAR
                              (300)  DEFAULT NULL,
    updated_by       INT UNSIGNED  DEFAULT NULL,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON
                              UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY
                              (id),
    UNIQUE KEY  uk_page_key
                              (page_key),
    CONSTRAINT  fk_page_editor
        FOREIGN KEY
                              (updated_by) REFERENCES users
                              (id)
        ON
                              DELETE
                              SET NULL
                              ON
                              UPDATE CASCADE
) ENGINE=InnoDB
                              DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Nội dung trang tĩnh - Giới thiệu, Chính sách...';

                              SET FOREIGN_KEY_CHECKS
                              = 1;

-- =============================================================
-- STORED FUNCTIONS  (chỉ giữ các hàm thực sự được dùng ở views)
-- =============================================================
DELIMITER $$

                              -- Giá hiệu lực: sale_price nếu có và > 0, không thì price
                              CREATE FUNCTION fn_get_effective_price(p_product_id INT UNSIGNED)
RETURNS DECIMAL
                              (15,2) DETERMINISTIC
                              BEGIN
                                DECLARE v_price      DECIMAL
                                (15,2) DEFAULT 0;
                              DECLARE v_sale_price DECIMAL
                              (15,2) DEFAULT NULL;
                              SELECT price, sale_price
                              INTO v_price
                              , v_sale_price
      FROM products WHERE id = p_product_id;
                              RETURN
                              IF(v_sale_price IS NOT NULL AND v_sale_price > 0, v_sale_price, v_price);
END$$

                              -- Điểm đánh giá trung bình của sản phẩm
                              CREATE FUNCTION fn_get_avg_rating(p_product_id INT UNSIGNED)
RETURNS DECIMAL
                              (3,1) DETERMINISTIC
                              BEGIN
                                DECLARE v_avg DECIMAL
                                (3,1) DEFAULT 0;
                              SELECT COALESCE(ROUND(AVG(rating), 1), 0)
                              INTO v_avg
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
AFTER
                              INSERT ON
                              order_items
                              FOR
                              EACH
                              ROW
                              BEGIN
                                IF NEW.product_id IS NOT NULL THEN
                                UPDATE products
           SET stock = stock - NEW.quantity
         WHERE id    = NEW.product_id;
                              END
                              IF;
END$$

                              CREATE TRIGGER trg_orderitem_after_delete
AFTER
                              DELETE ON order_items
FOR EACH
                              ROW
                              BEGIN
                                IF OLD.product_id IS NOT NULL THEN
                                UPDATE products
           SET stock = stock + OLD.quantity
         WHERE id    = OLD.product_id;
                              END
                              IF;
END$$

                              -- -------------------------------------------------------------
                              -- T2. ORDER STATUS -> hoàn / trừ stock khi hủy / khôi phục
                              -- -------------------------------------------------------------
                              CREATE TRIGGER trg_order_status_after_update
AFTER
                              UPDATE ON customer_orders
FOR EACH ROW
                              BEGIN
                                -- Chuyển sang cancelled: hoàn stock
                                IF NEW.status = 'cancelled' AND OLD.status <> 'cancelled' THEN
                                UPDATE products p
         INNER JOIN order_items oi
                                ON oi.product_id = p.id
                                SET p
                                .stock = p.stock + oi.quantity
         WHERE oi.order_id    = NEW.id
           AND oi.product_id IS NOT NULL;
                              END
                              IF;

    -- Khôi phục từ cancelled: trừ lại stock
    IF OLD.status = 'cancelled' AND NEW.status <> 'cancelled' THEN
                              UPDATE products p
         INNER JOIN order_items oi
                              ON oi.product_id = p.id
                              SET p
                              .stock = p.stock - oi.quantity
         WHERE oi.order_id    = NEW.id
           AND oi.product_id IS NOT NULL;
                              END
                              IF;
END$$

                              -- -------------------------------------------------------------
                              -- T3. REVIEWS - ràng buộc nghiệp vụ
                              --     Review phải thuộc đúng 1 đối tượng: product HOẶC article
                              --     Rating chỉ hợp lệ cho review sản phẩm
                              -- -------------------------------------------------------------
                              CREATE TRIGGER trg_review_before_insert
BEFORE
                              INSERT ON
                              reviews
                              FOR
                              EACH
                              ROW
                              BEGIN
                                IF NOT (
        (NEW.product_id IS NOT NULL AND NEW.article_id IS NULL)
                                  OR
                                  (NEW.product_id IS NULL AND NEW.article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
                                SET MESSAGE_TEXT
                                = 'Review phai thuoc dung 1 doi tuong: product_id hoac article_id.';
                              END
                              IF;

    IF NEW.product_id IS NULL AND NEW.rating IS NOT NULL THEN
        SIGNAL SQLSTATE '45000'
                              SET MESSAGE_TEXT
                              = 'Rating chi ap dung cho review san pham.';
                              END
                              IF;
END$$

                              CREATE TRIGGER trg_review_before_update
BEFORE
                              UPDATE ON reviews
FOR EACH ROW
                              BEGIN
                                IF NOT (
        (NEW.product_id IS NOT NULL AND NEW.article_id IS NULL)
                                  OR
                                  (NEW.product_id IS NULL AND NEW.article_id IS NOT NULL)
    ) THEN
        SIGNAL SQLSTATE '45000'
                                SET MESSAGE_TEXT
                                = 'Review phai thuoc dung 1 doi tuong: product_id hoac article_id.';
                              END
                              IF;
END$$

                              -- -------------------------------------------------------------
                              -- T4. PRODUCTS - chặn stock âm khi cập nhật trực tiếp,
                              --                kiểm tra sale_price < price (ngoài CHECK constraint)
                              -- -------------------------------------------------------------
                              CREATE TRIGGER trg_product_before_update
BEFORE
                              UPDATE ON products
FOR EACH ROW
                              BEGIN
                                IF NEW.stock < 0 THEN
                                SET NEW
                                .stock = 0;
                              END
                              IF;

    IF NEW.sale_price IS NOT NULL AND NEW.sale_price >= NEW.price THEN
        SIGNAL SQLSTATE '45000'
                              SET MESSAGE_TEXT
                              = 'sale_price phai nho hon price.';
                              END
                              IF;
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
                                LEFT JOIN categories     c ON c.id  = p.category_id
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
                                JOIN products   p ON p.id = ci.product_id
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

-- =============================================================
--  SAMPLE DATA - sportzone_db (BẢN FINAL ĐÃ FIX PASSWORD > 8 KÝ TỰ)
--  Import AFTER sportzone_db.sql
--
--  MẬT KHẨU CHUNG CHO TẤT CẢ TÀI KHOẢN: password123
--  Tài khoản test:
--    - admin@sportzone.vn   / password123   (role: admin)
--    - member@sportzone.vn  / password123   (role: member)
--    - phat@sportzone.vn    / password123   (role: member)
-- =============================================================

USE sportzone_db;

-- Tắt check FK để insert theo thứ tự tùy ý
SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- Xóa data cũ (nếu import lại) để tránh lỗi trùng lặp
DELETE FROM reviews;
DELETE FROM order_items;
DELETE FROM customer_orders;
DELETE FROM cart_items;
DELETE FROM product_images;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM articles;
DELETE FROM news_categories;
DELETE FROM faqs;
DELETE FROM faq_categories;
DELETE FROM contacts;
DELETE FROM pages;
DELETE FROM site_settings;
DELETE FROM users;

-- =============================================================
-- USERS (Đã fix mật khẩu dài hơn 8 ký tự: password123)
-- Mã Hash Bcrypt của chữ 'password123' là '$2y$10$nOUIs5kJ7naTuTFkBy1veuK0kSxUFXfuaOKdOKf9xYT0KKIGSJwFa'
-- =============================================================
INSERT INTO users (id, username, email, password_hash, full_name, phone, address, role, is_banned) VALUES
(1, 'admin',  'admin@sportzone.vn',  '$2y$10$12BzxUdqTRN520JLmBa3weFUkQqltUq.cq4neTIESmfTyNUaMO60O',
    'Quản Trị Viên', '0901234567', '123 Lý Tự Trọng, Q.1, TP.HCM', 'admin',  0),
(2, 'member', 'member@sportzone.vn', '$2y$10$12BzxUdqTRN520JLmBa3weFUkQqltUq.cq4neTIESmfTyNUaMO60O',
    'Nguyễn Văn A',  '0907654321', '456 Nguyễn Trãi, Q.5, TP.HCM',  'member', 0),
(3, 'phat',   'phat@sportzone.vn',   '$2y$10$12BzxUdqTRN520JLmBa3weFUkQqltUq.cq4neTIESmfTyNUaMO60O',
    'Văn Phát',      '0912345678', '789 Trần Hưng Đạo, Mỹ Tho, Tiền Giang', 'member', 0);


-- =============================================================
-- CATEGORIES (sản phẩm) - 2 cấp
-- =============================================================
INSERT INTO categories (id, name, slug, parent_id, sort_order, is_active) VALUES
(1, 'Bóng đá',        'bong-da',          NULL, 1, 1),
(2, 'Bóng rổ',        'bong-ro',          NULL, 2, 1),
(3, 'Cầu lông',       'cau-long',         NULL, 3, 1),
(4, 'Tennis',         'tennis',           NULL, 4, 1),
(5, 'Gym & Fitness',  'gym-fitness',      NULL, 5, 1),
(6, 'Giày bóng đá',   'giay-bong-da',        1, 1, 1),
(7, 'Vợt cầu lông',   'vot-cau-long',        3, 1, 1),
(8, 'Tạ & Dụng cụ',   'ta-dung-cu',          5, 1, 1);

-- =============================================================
-- PRODUCTS (20 sản phẩm)
-- =============================================================
INSERT INTO products (id, category_id, name, slug, description, price, sale_price, stock, sku, is_active, is_featured, meta_title, meta_description) VALUES
(1,  6, 'Giày bóng đá Nike Phantom GX',       'giay-bong-da-nike-phantom-gx',      'Giày đá bóng cao cấp, công nghệ Gripknit, dành cho sân cỏ tự nhiên.',        3500000.00, 2990000.00, 25,  'NIKE-PHGX-001', 1, 1, 'Giày bóng đá Nike Phantom GX chính hãng', 'Giày đá banh Nike Phantom GX giá tốt nhất tại SportZone'),
(2,  6, 'Giày bóng đá Adidas Predator',        'giay-bong-da-adidas-predator',      'Giày Predator Edge với công nghệ Zone Skin, ôm chân, kiểm soát bóng tốt.',    3200000.00, NULL,       18,  'ADI-PRED-002',  1, 1, 'Giày Adidas Predator chính hãng', 'Giày đá bóng Adidas Predator giá tốt'),
(3,  1, 'Bóng đá FIFA Quality Pro',            'bong-da-fifa-quality-pro',          'Bóng đá size 5, chứng nhận FIFA Quality Pro, da PU cao cấp.',                 850000.00,  699000.00,  50,  'BALL-FIFA-003', 1, 1, 'Bóng đá FIFA Quality Pro', 'Bóng đá tiêu chuẩn FIFA, chất lượng thi đấu'),
(4,  1, 'Găng tay thủ môn Nike GK Match',      'gang-tay-thu-mon-nike-gk',          'Găng tay thủ môn Nike, lòng bàn tay latex 4mm, ôm tay, chống trượt.',         650000.00,  NULL,       30,  'NIKE-GK-004',   1, 0, 'Găng tay thủ môn Nike GK Match', 'Găng tay thủ môn chất lượng cao'),
(5,  2, 'Bóng rổ Spalding NBA Official',       'bong-ro-spalding-nba',              'Bóng rổ size 7 chính thức NBA, da composite, chơi trong nhà & ngoài trời.',  1200000.00,  999000.00,  22,  'SPA-NBA-005',   1, 1, 'Bóng rổ Spalding NBA chính hãng', 'Bóng rổ Spalding size 7 tiêu chuẩn NBA'),
(6,  2, 'Giày bóng rổ Jordan 1 Mid',           'giay-bong-ro-jordan-1-mid',         'Giày bóng rổ Jordan 1 Mid, thiết kế cổ điển, đệm Air êm ái.',               2800000.00, 2500000.00,  15,  'JOR-MID-006',   1, 1, 'Giày Jordan 1 Mid chính hãng', 'Giày bóng rổ Jordan 1 Mid giá tốt'),
(7,  7, 'Vợt cầu lông Yonex Astrox 99',        'vot-cau-long-yonex-astrox-99',      'Vợt Yonex Astrox 99, công nghệ Rotational Generator, cân bằng đầu nặng.',    4200000.00, 3800000.00,  12,  'YNX-AX99-007',  1, 1, 'Vợt cầu lông Yonex Astrox 99', 'Vợt Yonex Astrox 99 giá tốt nhất'),
(8,  7, 'Vợt cầu lông Lining Halbertec 9000',  'vot-cau-long-lining-halbertec-9000','Vợt Lining Halbertec 9000, khung carbon nguyên khối, lực đập mạnh.',          3500000.00, NULL,       20,  'LIN-HB-008',    1, 0, 'Vợt Lining Halbertec 9000', 'Vợt cầu lông Lining chính hãng'),
(9,  3, 'Cầu lông Yonex Aerosensa 30',         'cau-long-yonex-aerosensa-30',       'Ống cầu lông Yonex AS-30 thi đấu chuyên nghiệp, 12 quả, lông ngỗng.',         520000.00,  480000.00,  80,  'YNX-AS30-009',  1, 0, 'Cầu lông Yonex Aerosensa 30', 'Cầu lông Yonex thi đấu'),
(10, 3, 'Giày cầu lông Yonex SHB Aerus Z',     'giay-cau-long-yonex-aerus-z',       'Giày cầu lông Yonex Aerus Z siêu nhẹ, đế chống trượt, bám sân cực tốt.',     2500000.00, NULL,       18,  'YNX-AERUS-010', 1, 0, 'Giày cầu lông Yonex Aerus Z', 'Giày Yonex Aerus Z siêu nhẹ'),
(11, 4, 'Vợt tennis Wilson Pro Staff 97',      'vot-tennis-wilson-pro-staff-97',    'Vợt tennis Wilson Pro Staff 97, công nghệ Braided Graphite, cảm giác bóng tốt.', 4800000.00, 4200000.00, 10, 'WIL-PS97-011',  1, 1, 'Vợt tennis Wilson Pro Staff 97', 'Vợt tennis Wilson chính hãng'),
(12, 4, 'Bóng tennis Wilson US Open (lon 3)',  'bong-tennis-wilson-us-open',        'Lon 3 quả bóng tennis Wilson US Open, áp suất chuẩn, bền.',                   180000.00,  150000.00, 100,  'WIL-USO-012',   1, 0, 'Bóng tennis Wilson US Open', 'Bóng tennis Wilson lon 3 quả'),
(13, 8, 'Tạ tay cao su 5kg (cặp)',             'ta-tay-cao-su-5kg',                 'Cặp tạ tay cao su 5kg, bọc chống trầy, cán thép, phù hợp tập tại nhà.',       450000.00,  399000.00,  40,  'GYM-DB5-013',   1, 0, 'Tạ tay cao su 5kg', 'Tạ tay 5kg tập gym tại nhà'),
(14, 8, 'Tạ tay cao su 10kg (cặp)',            'ta-tay-cao-su-10kg',                'Cặp tạ tay cao su 10kg, thiết kế 6 cạnh không lăn.',                          850000.00,  NULL,       25,  'GYM-DB10-014',  1, 0, 'Tạ tay cao su 10kg', 'Tạ tay 10kg chất lượng'),
(15, 5, 'Thảm yoga TPE 6mm',                   'tham-yoga-tpe-6mm',                 'Thảm yoga TPE cao cấp 6mm, chống trượt 2 mặt, kích thước 183x61cm.',          320000.00,  249000.00,  60,  'YOGA-TPE-015',  1, 1, 'Thảm yoga TPE 6mm cao cấp', 'Thảm yoga TPE dày 6mm giá tốt'),
(16, 5, 'Dây nhảy thể dục tốc độ cao',         'day-nhay-the-duc',                  'Dây nhảy cáp thép bọc PVC, điều chỉnh chiều dài, đếm số tự động.',            180000.00,  NULL,       70,  'GYM-JR-016',    1, 0, 'Dây nhảy tốc độ cao', 'Dây nhảy thể dục giảm mỡ'),
(17, 5, 'Bình nước thể thao Nike 1L',          'binh-nuoc-nike-1l',                 'Bình nước thể thao Nike 1000ml, nhựa BPA-free, nắp khóa an toàn.',            280000.00,  250000.00,  90,  'NIKE-WB-017',   1, 0, 'Bình nước Nike 1L', 'Bình nước thể thao Nike chính hãng'),
(18, 1, 'Áo đấu bóng đá CLB Việt Nam',         'ao-dau-bong-da-viet-nam',           'Áo đấu đội tuyển Việt Nam 2024, vải Dri-Fit thoáng khí, logo thêu.',         750000.00,  680000.00,  45,  'VN-JR-018',     1, 1, 'Áo đấu đội tuyển Việt Nam', 'Áo bóng đá Việt Nam chính hãng'),
(19, 1, 'Vớ bóng đá Nike Academy',             'vo-bong-da-nike-academy',           'Vớ bóng đá Nike Academy dài, co giãn, ôm bắp chân, chống hôi.',               180000.00,  NULL,       150, 'NIKE-SOCK-019', 1, 0, 'Vớ bóng đá Nike Academy', 'Vớ đá banh Nike chính hãng'),
(20, 8, 'Dây kháng lực (set 5 dây)',           'day-khang-luc-5-day',               'Bộ 5 dây kháng lực các mức lực từ 5-25kg, tặng kèm túi đựng.',                250000.00,  199000.00,  55,  'GYM-RB-020',    1, 0, 'Dây kháng lực 5 mức', 'Bộ dây kháng lực tập gym tại nhà');

-- =============================================================
-- PRODUCT_IMAGES
-- =============================================================
INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order) VALUES
(1,  'uploads/nike-phantom-gx.jpg',   'Giày Nike Phantom GX',      1, 1),
(2,  'uploads/adidas-predator.jpg',   'Giày Adidas Predator',      1, 1),
(3,  'uploads/fifa-ball.jpg',         'Bóng đá FIFA Quality Pro',  1, 1),
(4,  'uploads/nike-gk-gloves.jpg',    'Găng tay Nike GK',          1, 1),
(5,  'uploads/spalding-nba.jpg',      'Bóng rổ Spalding NBA',      1, 1),
(6,  'uploads/jordan-1-mid.jpg',      'Giày Jordan 1 Mid',         1, 1),
(7,  'uploads/yonex-astrox-99.jpg',   'Vợt Yonex Astrox 99',       1, 1),
(8,  'uploads/lining-halbertec.jpg',  'Vợt Lining Halbertec 9000', 1, 1),
(9,  'uploads/yonex-as30.jpg',        'Cầu lông Yonex AS-30',      1, 1),
(10, 'uploads/yonex-aerus-z.jpg',     'Giày Yonex Aerus Z',        1, 1),
(11, 'uploads/wilson-ps97.jpg',       'Vợt Wilson Pro Staff 97',   1, 1),
(12, 'uploads/wilson-us-open.jpg',    'Bóng tennis Wilson',        1, 1),
(13, 'uploads/dumbbell-5kg.jpg',      'Tạ tay cao su 5kg',         1, 1),
(14, 'uploads/dumbbell-10kg.jpg',     'Tạ tay cao su 10kg',        1, 1),
(15, 'uploads/yoga-mat-tpe.jpg',      'Thảm yoga TPE 6mm',         1, 1),
(16, 'uploads/jump-rope.jpg',         'Dây nhảy',                  1, 1),
(17, 'uploads/nike-water-bottle.jpg', 'Bình nước Nike 1L',         1, 1),
(18, 'uploads/vietnam-jersey.jpg',    'Áo đội tuyển Việt Nam',     1, 1),
(19, 'uploads/nike-socks.jpg',        'Vớ Nike Academy',           1, 1),
(20, 'uploads/resistance-bands.jpg',  'Dây kháng lực',             1, 1),
(1,  'uploads/nike-phantom-gx-2.jpg', 'Giày Nike Phantom GX góc nghiêng', 0, 2),
(1,  'uploads/nike-phantom-gx-3.jpg', 'Giày Nike Phantom GX đế',          0, 3),
(7,  'uploads/yonex-astrox-99-2.jpg', 'Vợt Yonex Astrox 99 cán',          0, 2);

-- =============================================================
-- NEWS_CATEGORIES & ARTICLES
-- =============================================================
INSERT INTO news_categories (id, name, slug, sort_order, is_active) VALUES
(1, 'Tin thể thao',      'tin-the-thao',    1, 1),
(2, 'Hướng dẫn chọn đồ', 'huong-dan',       2, 1),
(3, 'Review sản phẩm',   'review',          3, 1),
(4, 'Khuyến mãi',        'khuyen-mai',      4, 1),
(5, 'Sự kiện',           'su-kien',         5, 1);

INSERT INTO articles (id, author_id, category_id, title, slug, summary, content, thumbnail, meta_title, meta_description, meta_keywords, is_published, is_featured, views, published_at) VALUES
(1, 1, 2, 'Cách chọn giày bóng đá phù hợp với mặt sân',
    'cach-chon-giay-bong-da-phu-hop',
    'Hướng dẫn chi tiết cách chọn giày đá bóng theo từng loại mặt sân: cỏ tự nhiên, cỏ nhân tạo, futsal.',
    '<p>Việc chọn giày bóng đá phù hợp với mặt sân là yếu tố rất quan trọng...</p><h3>1. Giày FG (Firm Ground)</h3><p>Dùng cho sân cỏ tự nhiên...</p><h3>2. Giày AG (Artificial Ground)</h3><p>Dùng cho sân cỏ nhân tạo...</p><h3>3. Giày TF (Turf)</h3><p>Dùng cho sân cỏ nhân tạo mỏng...</p>',
    'uploads/adidas-predator.jpg',
    'Cách chọn giày bóng đá phù hợp mặt sân - SportZone',
    'Tư vấn chọn giày đá bóng FG, AG, TF, IC phù hợp với từng loại sân cỏ.',
    'giày bóng đá, chọn giày đá banh, giày FG, giày AG',
    1, 1, 1250, '2025-09-15 10:00:00'),
(2, 1, 3, 'Review Nike Phantom GX: Đỉnh cao của giày kiểm soát bóng',
    'review-nike-phantom-gx',
    'Đánh giá chi tiết mẫu giày Nike Phantom GX về thiết kế, công nghệ Gripknit và trải nghiệm thực tế.',
    '<p>Nike Phantom GX là thế hệ tiếp theo của dòng giày kiểm soát bóng Nike...</p><h3>Thiết kế</h3><p>Upper Gripknit bao phủ toàn bộ mặt giày...</p><h3>Trải nghiệm</h3><p>Sau 10 trận thi đấu, chúng tôi nhận thấy...</p>',
    'uploads/nike-phantom-gx-2.jpg',
    'Review Nike Phantom GX chi tiết - SportZone',
    'Đánh giá giày Nike Phantom GX: công nghệ, thiết kế, trải nghiệm thực tế.',
    'nike phantom gx, review giày bóng đá, giày nike',
    1, 1, 980, '2025-09-20 14:30:00'),
(3, 1, 2, 'Hướng dẫn chọn vợt cầu lông cho người mới chơi',
    'huong-dan-chon-vot-cau-long',
    'Những lưu ý quan trọng khi chọn vợt cầu lông: trọng lượng, độ cứng cán, cân bằng đầu.',
    '<p>Cầu lông là môn thể thao yêu cầu vợt phải phù hợp với thể lực và phong cách chơi...</p><h3>Trọng lượng vợt</h3><p>Vợt 3U (85-89g) phù hợp đa số người chơi...</p><h3>Độ cứng cán</h3><p>Cán dẻo phù hợp người mới, cán cứng cho người chơi mạnh...</p>',
    'uploads/yonex-astrox-99-2.jpg',
    'Hướng dẫn chọn vợt cầu lông cho người mới',
    'Tư vấn chọn vợt cầu lông: trọng lượng, độ cứng cán, cân bằng vợt.',
    'vợt cầu lông, chọn vợt, yonex, lining',
    1, 0, 620, '2025-09-25 09:00:00'),
(4, 1, 1, 'ĐT Việt Nam chuẩn bị cho AFF Cup 2026',
    'dt-viet-nam-chuan-bi-aff-cup-2026',
    'Thông tin mới nhất về quá trình chuẩn bị của đội tuyển Việt Nam cho giải AFF Cup sắp tới.',
    '<p>Đội tuyển bóng đá Việt Nam đang có đợt tập trung quan trọng...</p><p>HLV trưởng đã triệu tập 30 cầu thủ cho đợt tập huấn này...</p>',
    'uploads/vietnam-jersey.jpg',
    'ĐT Việt Nam chuẩn bị cho AFF Cup 2026',
    'Cập nhật tin tức ĐT Việt Nam chuẩn bị cho AFF Cup 2026.',
    'đội tuyển việt nam, AFF Cup, bóng đá',
    1, 1, 2100, '2025-10-01 08:00:00'),
(5, 1, 4, 'Khuyến mãi tháng 10: Giảm đến 30% toàn bộ giày thể thao',
    'khuyen-mai-thang-10-giay-the-thao',
    'Đón mùa thu, SportZone giảm giá sâu nhiều mẫu giày bóng đá, bóng rổ, cầu lông.',
    '<p>Chương trình khuyến mãi áp dụng từ 01/10 đến 31/10/2025...</p><ul><li>Giày Nike giảm 20%</li><li>Giày Adidas giảm 25%</li><li>Giày Yonex giảm 30%</li></ul>',
    'uploads/nike-phantom-gx-3.jpg',
    'Khuyến mãi tháng 10 - Giảm đến 30% giày thể thao',
    'Chương trình sale tháng 10 tại SportZone: giảm sâu nhiều mẫu giày.',
    'khuyến mãi, sale, giảm giá, giày thể thao',
    1, 1, 3400, '2025-10-01 00:00:00'),
(9, 1, 2, 'Hướng dẫn bảo quản giày thể thao đúng cách',
    'huong-dan-bao-quan-giay-the-thao',
    'Bài viết đang soạn thảo...',
    '<p>Nội dung chưa hoàn thiện...</p>',
    NULL, NULL, NULL, NULL,
    0, 0, 0, NULL);

-- =============================================================
-- FAQS & CATEGORIES
-- =============================================================
INSERT INTO faq_categories (id, name, sort_order, is_active) VALUES
(1, 'Đặt hàng & Thanh toán', 1, 1),
(2, 'Vận chuyển & Giao hàng', 2, 1),
(3, 'Đổi trả & Bảo hành',     3, 1);

INSERT INTO faqs (category_id, question, answer, sort_order, is_active) VALUES
(1, 'Làm sao để đặt hàng trên SportZone?', 'Bạn có thể đặt hàng qua 3 bước: (1) Thêm sản phẩm vào giỏ hàng, (2) Vào giỏ hàng và bấm Thanh toán, (3) Điền thông tin nhận hàng và xác nhận đơn.', 1, 1),
(1, 'SportZone hỗ trợ những phương thức thanh toán nào?', 'Hiện tại chúng tôi hỗ trợ: COD (thanh toán khi nhận hàng), chuyển khoản ngân hàng, Momo, ZaloPay.', 2, 1),
(2, 'Thời gian giao hàng là bao lâu?', 'Nội thành TP.HCM và Hà Nội: 1-2 ngày. Các tỉnh thành khác: 3-5 ngày làm việc.', 1, 1),
(3, 'Chính sách đổi trả của SportZone ra sao?', 'Đổi trả miễn phí trong 7 ngày kể từ ngày nhận hàng nếu sản phẩm còn nguyên tem, chưa qua sử dụng. Áp dụng với lỗi do nhà sản xuất hoặc giao sai sản phẩm.', 1, 1);

-- =============================================================
-- CUSTOMER_ORDERS & ITEMS
-- =============================================================
INSERT INTO customer_orders (id, user_id, recipient_name, recipient_phone, shipping_address, total_amount, status, note, created_at) VALUES
(1, 2, 'Nguyễn Văn A', '0907654321', '456 Nguyễn Trãi, Q.5, TP.HCM', 0.00, 'delivered',  'Giao giờ hành chính',  '2025-09-10 10:30:00'),
(2, 3, 'Văn Phát',     '0912345678', '789 Trần Hưng Đạo, Mỹ Tho, Tiền Giang', 0.00, 'processing', 'Gọi trước khi giao',    '2025-10-15 14:20:00'),
(3, 2, 'Nguyễn Văn A', '0907654321', '456 Nguyễn Trãi, Q.5, TP.HCM', 0.00, 'pending',    NULL,                    '2025-10-22 09:15:00');

INSERT INTO order_items (order_id, product_id, product_name, price_at_order, quantity) VALUES
(1, 1, 'Giày bóng đá Nike Phantom GX', 2990000.00, 1),
(1, 3, 'Bóng đá FIFA Quality Pro',      699000.00, 1),
(2, 7, 'Vợt cầu lông Yonex Astrox 99', 3800000.00, 1),
(2, 9, 'Cầu lông Yonex Aerosensa 30',   480000.00, 2),
(3, 15, 'Thảm yoga TPE 6mm',            249000.00, 2),
(3, 16, 'Dây nhảy thể dục tốc độ cao',  180000.00, 1);

UPDATE customer_orders co SET total_amount = (SELECT COALESCE(SUM(price_at_order * quantity), 0) FROM order_items WHERE order_id = co.id);

-- =============================================================
-- REVIEWS & CONTACTS
-- =============================================================
INSERT INTO reviews (user_id, product_id, article_id, content, rating, is_approved, created_at) VALUES
(2, 1, NULL, 'Giày đẹp, đá sướng chân, bám sân tốt. Rất đáng tiền!',            5, 1, '2025-09-20 11:00:00'),
(3, 1, NULL, 'Chất lượng ổn, giao hàng nhanh. Size hơi nhỏ nên đặt tăng 0.5.', 4, 1, '2025-09-22 16:30:00'),
(2, 7, NULL, 'Vợt nặng đầu, đập cầu phê, khuyên dùng cho người chơi tấn công.',  5, 1, '2025-10-01 20:00:00'),
(3, 11, NULL, 'Vợt cầm đầm tay, chưa đánh thử được trận nào.',                 4, 0, '2025-10-20 10:00:00'),
(2, NULL, 2, 'Bài review rất chi tiết, cảm ơn shop. Mình vừa mua về dùng cũng thấy đúng như mô tả.', NULL, 1, '2025-09-21 08:00:00');

INSERT INTO contacts (user_id, name, email, phone, subject, message, status, admin_note, created_at) VALUES
(NULL, 'Trần Thị B',   'tranthib@gmail.com', '0905111222', 'Hỏi về sản phẩm Nike Phantom GX', 'Shop ơi cho hỏi giày Nike Phantom GX có size 43 không ạ? Mình cần gấp.', 'unread', NULL, '2025-10-22 10:00:00'),
(2,    'Nguyễn Văn A', 'member@sportzone.vn','0907654321', 'Đổi size giày',                    'Mình vừa nhận giày size 42 nhưng hơi rộng, có thể đổi sang 41.5 không?',  'read', NULL, '2025-10-20 16:30:00');

-- =============================================================
-- SITE_SETTINGS & PAGES (BẢN ĐỦ KEY CHO ADMIN)
-- =============================================================
INSERT INTO site_settings (setting_key, setting_value, group_name, label, input_type) VALUES
('site_name',              'SportZone Vietnam',                                               'general', 'Tên website',           'text'),
('site_tagline',           'Dụng cụ thể thao chính hãng - Giá tốt',                           'general', 'Slogan',                'text'),
('site_logo',              'uploads/settings/logo.png',                                       'general', 'Logo',                  'image'),
('site_favicon',           'uploads/settings/favicon.ico',                                    'general', 'Favicon',               'image'),
('contact_phone',          '1900-6969',                                                       'contact', 'Hotline',               'text'),
('contact_email',          'support@sportzone.vn',                                            'contact', 'Email hỗ trợ',          'email'),
('contact_address',        '123 Lý Tự Trọng, Q.1, TP.HCM',                                    'contact', 'Địa chỉ công ty',       'text'),
('store_address',          '123 Lý Tự Trọng, Q.1, TP.HCM',                                    'contact', 'Địa chỉ cửa hàng',      'text'),
('facebook_url',           'https://facebook.com/sportzone.vn',                               'social',  'Facebook',              'url'),
('youtube_url',            'https://youtube.com/@sportzone',                                  'social',  'YouTube',               'url'),
('social_facebook',        'https://facebook.com/sportzone.vn',                               'social',  'Facebook URL',          'url'),
('social_instagram',       'https://instagram.com/sportzone.vn',                              'social',  'Instagram URL',         'url'),
('social_youtube',         'https://youtube.com/@sportzone',                                  'social',  'YouTube URL',           'url'),
('meta_keywords',          'dụng cụ thể thao, giày bóng đá, vợt cầu lông, bóng rổ',           'seo',     'Meta keywords',         'text'),
('meta_description',       'SportZone - Shop dụng cụ thể thao chính hãng.',                   'seo',     'Meta description',      'text'),
('currency',               'VND',                                                             'store',   'Đơn vị tiền tệ',        'text'),
('shipping_fee',           '30000',                                                           'store',   'Phí vận chuyển',        'number'),
('free_shipping_threshold','500000',                                                          'store',   'Miễn phí ship từ',      'number'),
('allow_backorder',        '0',                                                               'store',   'Cho phép backorder',    'checkbox'),
('site_active',            '1',                                                               'store',   'Site đang hoạt động',   'checkbox'),
('about_title',            'Về SportZone Vietnam',                                            'about',   'Tiêu đề giới thiệu',    'text'),
('about_description',      'Chúng tôi cung cấp giải pháp để bạn chinh phục mọi giới hạn.',    'about',   'Mô tả ngắn',            'textarea'),
('about_mission',          'Mang đến những thiết bị thể thao chất lượng nhất.',               'about',   'Sứ mệnh',               'textarea'),
('about_vision',           'Trở thành điểm đến số 1 cho cộng đồng yêu thể thao.',             'about',   'Tầm nhìn',              'textarea'),
('about_values',           'Chất lượng hàng đầu - Phục vụ tận tâm - Sáng tạo không ngừng.',   'about',   'Giá trị cốt lõi',       'textarea');

INSERT INTO pages (page_key, title, content, meta_title, meta_description, updated_by) VALUES
('about', 'Giới thiệu SportZone Vietnam', '<h2>Về chúng tôi</h2><p>SportZone Vietnam được thành lập năm 2020...</p>', 'Giới thiệu SportZone Vietnam', 'SportZone Vietnam - Nhà phân phối dụng cụ thể thao.', 1),
('policy', 'Chính sách bảo mật', '<h2>Chính sách bảo mật</h2><p>SportZone cam kết bảo vệ thông tin...</p>', 'Chính sách bảo mật - SportZone', 'Chính sách bảo mật thông tin khách hàng.', 1),
('shipping', 'Chính sách vận chuyển', '<h2>Vận chuyển & Giao hàng</h2><p>SportZone giao hàng toàn quốc...</p>', 'Chính sách vận chuyển - SportZone', 'Chính sách và thời gian vận chuyển hàng.', 1);

COMMIT;
SET AUTOCOMMIT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
--  DATABASE : chatbot_db
--  PROJECT  : SportZone Vietnam - Website Dụng cụ Thể thao
--  SUBJECT  : Lập trình Web (HK2 2025-2026)
--  ENGINE   : InnoDB   (FK, transaction)
--  CHARSET  : utf8mb4  (tiếng Việt + emoji)
--  NORM     : 3NF
-- =============================================================

DROP DATABASE IF EXISTS chatbot_db;
CREATE DATABASE chatbot_db; 
USE chatbot_db;

SET FOREIGN_KEY_CHECKS = 0;
-- BẢNG 1: QUẢN LÝ MỘT CHATBOT CỦA MỘT USER (ONE USER - ONE  BOT)
CREATE TABLE chat_session (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  session_token VARCHAR(255) NOT NULL COMMENT 'Token định danh phiên làm việc của chatbot (có thể dùng để xác thực API)',
  user_id VARCHAR(50) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uk_session_token(session_token) COMMENT 'Đảm bảo mỗi phiên làm việc có một token duy nhất'
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý chatbot của từng user, đảm bảo mỗi user chỉ có một chatbot duy nhất';

-- BẢNG 2: LƯU TRỮ CHI TIẾT TIN NHẮN (CHAT MESSAGES)
CREATE TABLE chat_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  chatbot_id INT UNSIGNED NOT NULL,
  message_text TEXT NOT NULL,
  sender ENUM('user', 'bot') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  FOREIGN KEY (chatbot_id) REFERENCES chat_session(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu trữ chi tiết tin nhắn giữa user và chatbot, liên kết với chat_session qua chatbot_id';

-- BẢNG 3: TRI THỨC BOT / TRẢ LỜI NHANH (BOT_KNOWLEDGE_BASE)
CREATE TABLE bot_knowledge_base (
  id INT AUTO_INCREMENT,
  keyword VARCHAR(255) NOT NULL COMMENT 'Từ khóa (Ví dụ: "hoàn tiền", "bảng giá", "liên hệ")',
  response_text TEXT NOT NULL COMMENT 'Câu trả lời tương ứng',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uk_keyword(keyword) COMMENT 'Đảm bảo mỗi từ khóa là duy nhất'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng tri thức của bot, chứa các cặp từ khóa và câu trả lời để bot có thể phản hồi nhanh dựa trên từ khóa';

-- Bật lại kiểm tra khóa ngoại sau khi cấu trúc được dựng xong
SET FOREIGN_KEY_CHECKS = 1;G R A N T   A L L   P R I V I L E G E S   O N   c h a t b o t _ d b . *   T O   ' s p o r t z o n e ' @ ' % ' ;   F L U S H   P R I V I L E G E S ;  
 