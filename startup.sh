#!/bin/bash

echo "=== KHỞI ĐỘNG TIẾN TRÌNH CẤU HÌNH HẠ TẦNG ==="

# Đổi đường dẫn đích từ sites-available/default sang conf.d/default.conf
if [ -f "/home/site/wwwroot/nginx.conf" ]; then
    echo "Tìm thấy file nginx.conf tùy biến. Tiến hành nạp..."
    cp /home/site/wwwroot/nginx.conf /etc/nginx/conf.d/default.conf
else
    echo "CẢNH BÁO: Không tìm thấy file nginx.conf tại /home/site/wwwroot/"
fi

# Kiểm tra cú pháp hệ thống
nginx -t
if [ $? -eq 0 ]; then
    echo "Cú pháp Nginx chuẩn xác. Đang khởi động lại dịch vụ..."
    service nginx restart
else
    echo "LỖI: Cú pháp Nginx bị sai. Hủy bỏ."
    exit 1
fi

echo "=== CẤU HÌNH HOÀN TẤT ==="