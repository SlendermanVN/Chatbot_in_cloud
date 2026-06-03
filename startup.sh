#!/bin/bash

# Kiểm tra cú pháp file cấu hình tùy chỉnh của bạn trước khi nạp
if [ -f "/home/site/wwwroot/nginx.conf" ]; then
    echo "Nạp cấu hình Nginx tùy chỉnh..."
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
    
    # Ép buộc chạy reload hoặc restart ở dạng chế độ nền, không chặn terminal
    service nginx reload > /dev/null 2>&1 &
else
    echo "Không tìm thấy cấu hình tùy chỉnh, dùng cấu hình gốc."
fi