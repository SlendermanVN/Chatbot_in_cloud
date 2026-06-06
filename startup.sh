#!/bin/bash

echo "=== KHỞI ĐỘNG TIẾN TRÌNH CẤU HÌNH HẠ TẦNG ==="

# 1. Kiểm tra xem file cấu hình Nginx tùy biến có tồn tại không
if [ -f "/home/site/wwwroot/nginx.conf" ]; then
    echo "Tìm thấy file nginx.conf tùy biến. Tiến hành nạp..."
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
else
    echo "CẢNH BÁO: Không tìm thấy file nginx.conf tại /home/site/wwwroot/"
fi

# 2. Kiểm tra cú pháp Nginx trước khi restart để tránh sập web
nginx -t
if [ $? -eq 0 ]; then
    echo "Cú pháp Nginx chuẩn xác. Đang khởi động lại dịch vụ..."
    service nginx restart
else
    echo "LỖI: Cú pháp Nginx bị sai. Hủy bỏ restart để giữ hệ thống hoạt động."
    exit 1
fi

echo "=== CẤU HÌNH HOÀN TẤT ==="