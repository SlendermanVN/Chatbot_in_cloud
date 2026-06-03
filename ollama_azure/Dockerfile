# ============================================================
# DOCKERFILE - ĐÓNG GÓI OLLAMA TÍCH HỢP SẴN MODEL AI CHO CLOUD
# ============================================================
FROM ollama/ollama:latest

# Thiết lập biến môi trường bắt buộc cho môi trường Docker
ENV OLLAMA_HOST=0.0.0.0
ENV OLLAMA_NUM_PARALLEL=4

# Chạy một tiến trình build tạm thời để tải sẵn Model vào Image (Pre-baked Model)
RUN ollama serve & \
  sleep 5 && \
  ollama run qwen2.5:1.5b && \
  pkill ollama

# Cổng mạng mặc định của dịch vụ Ollama
EXPOSE 11434

# Lệnh khởi chạy chính thức khi Container lên Azure
ENTRYPOINT ["ollama", "serve"]