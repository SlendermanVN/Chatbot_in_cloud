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