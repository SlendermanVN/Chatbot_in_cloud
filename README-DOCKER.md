# Docker & Docker Swarm Guide for SportZone (Step-by-step)

Tài liệu này mô tả từng bước thiết lập, build image, thiết lập biến môi trường, deploy stack trên Docker Swarm và kiểm tra các service (Prometheus + Grafana + exporters + autoscaler).

---

## A. Yêu cầu trước khi bắt đầu

- Docker Engine (v20+) với quyền `docker` CLI trên máy deploy
- Đã clone repo và đang đứng ở thư mục gốc `main-repo/`

Phù hợp cho Linux/macOS (bash) và Windows PowerShell. Ở ví dụ dưới tôi cung cấp cả hai khi cần.

---

## B. Các file quan trọng

- `Dockerfile` — build `sportzone-web` image
- `docker-stack.yml` — stack definition (services, configs)
- `scripts/swarm-autoscale.sh` — autoscaler script
- `scripts/mysqld-exporter-start.sh` — wrapper mysqld_exporter
- `monitoring/` — Prometheus, Alertmanager, Grafana provisioning

---

## C. 1 — Build image local

Chạy từ thư mục gốc `main-repo`.

Linux/macOS:

```bash
docker build -t sportzone-web:latest .
```

Windows PowerShell:

```powershell
docker build -t sportzone-web:latest .
```

Nếu muốn đẩy lên registry private (ví dụ `registry.example.com`):

```bash
docker tag sportzone-web:latest registry.example.com/project/sportzone-web:latest
docker push registry.example.com/project/sportzone-web:latest
```

---

## C.2 — Cấu hình biến môi trường

Ứng dụng không còn sử dụng Docker Secrets mà thay thế bằng các biến môi trường trực tiếp cho DB, Web và exporter.

Bạn có thể cung cấp các biến môi trường này thông qua terminal shell session hiện tại trước lúc deploy, hoặc ghi vào file `.env`.

Ví dụ các biến bắt buộc:

```properties
# Thông tin cấu hình mật khẩu
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_PASSWORD=your_db_password
DB_PASS=your_db_password

# API Key cho chatbot
GEMINI_API_KEY=your_gemini_key
```

---

## C.3 — Tạo thư mục lưu uploads trên host (nếu dùng bind mount)

Trong `docker-stack.yml` hiện tại `uploads_shared` bind tới `/var/lib/sportzone/uploads_shared`. Tạo và gán permission trên mỗi node nếu cần.

Linux:

```bash
sudo mkdir -p /var/lib/sportzone/uploads_shared
sudo chown -R 33:33 /var/lib/sportzone/uploads_shared
```

Windows: nếu deploy trên Windows node, thay đường dẫn tương ứng hoặc dùng volume thay vì bind.

Lưu ý: với môi trường multi-node, cân nhắc dùng S3 / NFS / CSI driver thay vì bind mount.

---

## D — (Nếu cần) Khởi tạo Swarm manager

Nếu máy chưa là manager:

```bash
docker swarm init
```

Để join worker node, chạy lệnh `docker swarm join ...` do `docker swarm init` trả về trên các node khác.

---

## E — Kiểm tra cấu hình compose trước khi deploy

Ở máy có Docker Engine, kiểm tra file stack:

```bash
docker compose -f docker-stack.yml config
```

Sửa lỗi YAML nếu lệnh báo cáo.

---

## F — Deploy stack (trên Swarm manager)

Sau khi export cấu hình environment (hoặc đảm bảo trong `.env`), tiến hành deploy từ thư mục gốc repo.

Linux/macOS:

```bash
export MYSQL_ROOT_PASSWORD=rootpass
export MYSQL_PASSWORD=sportzonepass
export DB_PASS=sportzonepass
export GEMINI_API_KEY=YOUR_KEY
docker stack deploy -c docker-stack.yml sportzone
```

PowerShell (Windows):

```powershell
$env:MYSQL_ROOT_PASSWORD="rootpass"; $env:MYSQL_PASSWORD="sportzonepass"; $env:DB_PASS="sportzonepass"; $env:GEMINI_API_KEY="YOUR_KEY"
docker stack deploy -c docker-stack.yml sportzone
```

Kiểm tra trạng thái:

```bash
docker stack services sportzone
docker stack ps sportzone
```

Xem logs (ví dụ web):

```bash
docker service logs -f sportzone_web
```

---

## G — URLs & truy cập

- Ứng dụng web: http://<MANAGER_HOST>:8080 (stack publish port 8080 → service 80)
- Prometheus: http://<MANAGER_HOST>:9090
- Alertmanager: http://<MANAGER_HOST>:9093
- Grafana: http://<MANAGER_HOST>:3000 (user `admin` / password `admin123` theo config hiện tại)

---

## H — Kiểm tra nhanh sau deploy

1. Kiểm tra tất cả services đã chạy:

```bash
docker service ls
```

2. Xác minh Prometheus scrape targets: mở Prometheus → `Status -> Targets`
3. Kiểm tra Alertmanager: `http://<MANAGER_HOST>:9093`
4. Kiểm tra exporter mysqld: Prometheus target `mysqld_exporter` (job) và metric `mysql_up` / `mysql_global_status_threads_connected`.

---

## I — Cấu hình Alertmanager (chú ý bảo mật)

- File config hiện tại: `monitoring/alertmanager/alertmanager.yml` (cài dưới `configs` trong `docker-stack.yml`).
- Thay placeholder webhook / SMTP bằng giá trị thật trước khi deploy, hoặc tạo secret để lưu các webhook/credential.

Ví dụ nhanh sửa Slack webhook trong Alertmanager config:

```yaml
receivers:
- name: slack-alerts
slack_configs:
- api_url: 'https://hooks.slack.com/services/REPLACE/THIS/HOOK'
channel: '#alerts'
```

---

## J — Stop & remove stack

```bash
docker stack rm sportzone
```

---

## K — Gợi ý production

- Dùng managed DB/Redis hoặc triển khai HA cho dữ liệu (MySQL/Galera, Redis Sentinel/Cluster).
- KHI LÊN PRODUCTION THẬT SỰ KHUYẾN CÁO SỬ DỤNG DOCKER SECRETS HOẶC VAULT CHO `MYSQL_ROOT_PASSWORD`, `MYSQL_PASSWORD` hay `GEMINI_API_KEY` chứ không truyền trần trụi dưới dạng Shell Environment.
- Dùng shared storage (S3, NFS, or CSI) cho `uploads_shared` trên multi-node.
- Cân nhắc chuyển sang Kubernetes + Prometheus Operator cho scale/observability production.
