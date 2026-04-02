# rophim-test2

## Hướng dẫn deploy `tinix-ai/tinix-bazi` lên CyberPanel + Ubuntu

> Mục tiêu: chạy **frontend React (Vite build)** qua OpenLiteSpeed và chạy **backend Node.js (Express)** bằng PM2, sau đó reverse proxy `/api` từ domain chính về backend port nội bộ.

---

## 1) Chuẩn bị server Ubuntu

```bash
sudo apt update && sudo apt -y upgrade
sudo apt install -y git curl build-essential
```

Cài Node.js LTS (khuyến nghị Node 20):

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v
npm -v
```

Cài PM2 để quản lý backend:

```bash
sudo npm i -g pm2
pm2 -v
```

---

## 2) Clone source và cài dependencies

```bash
cd /home
sudo git clone https://github.com/tinix-ai/tinix-bazi.git
sudo chown -R $USER:$USER /home/tinix-bazi
cd /home/tinix-bazi
npm install
```

Dự án này dùng npm workspaces (`frontend`, `backendjs`).

---

## 3) Cấu hình biến môi trường backend

```bash
cd /home/tinix-bazi
cp backendjs/.env.example backendjs/.env
nano backendjs/.env
```

Thiết lập tối thiểu (ví dụ):

```env
PORT=8888
NODE_ENV=production
JWT_SECRET=thay_bang_chuoi_bi_mat_rat_dai
OPENROUTER_API_KEY=your_openrouter_key
```

> Nếu app của bạn có thêm biến môi trường khác trong code backend, hãy bổ sung đầy đủ trước khi chạy production.

---

## 4) Build frontend production

```bash
cd /home/tinix-bazi
npm run build -w frontend
```

Sau khi build xong, static files nằm ở:

```text
/home/tinix-bazi/frontend/dist
```

---

## 5) Chạy backend bằng PM2

```bash
cd /home/tinix-bazi/backendjs
pm2 start server.js --name tinix-bazi-api
pm2 save
pm2 startup
```

Kiểm tra:

```bash
pm2 status
curl http://127.0.0.1:8888
```

---

## 6) Cấu hình website trên CyberPanel

Trong CyberPanel:

1. **Websites → Create Website**
   - Domain: `yourdomain.com`
   - PHP có thể để mặc định (không dùng PHP cho app này).

2. **File Manager**
   - Document root thường là:  
     `/home/yourdomain.com/public_html`

3. Xóa file mặc định trong `public_html` và copy frontend build vào:

```bash
sudo rsync -av --delete /home/tinix-bazi/frontend/dist/ /home/yourdomain.com/public_html/
sudo chown -R yourdomain.com:yourdomain.com /home/yourdomain.com/public_html
```

> Thay `yourdomain.com` đúng theo user/domain mà CyberPanel tạo.

---

## 7) Reverse proxy `/api` về Node backend (OpenLiteSpeed)

Vì frontend gọi API qua `/api`, cấu hình rewrite/proxy ở vhost domain.

Mở file rewrite của domain (CyberPanel thường sinh sẵn):

```bash
sudo nano /usr/local/lsws/conf/vhosts/yourdomain.com/vhconf.conf
```

Thêm/đảm bảo có các rule dạng sau trong phần rewrite:

```apache
rewrite  {
  enable                  1
  autoLoadHtaccess        1

  # API -> backend Node.js
  RewriteRule ^/api/(.*)$ http://127.0.0.1:8888/api/$1 [P,L]

  # SPA fallback (React Router)
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ /index.html [L]
}
```

Sau đó reload OpenLiteSpeed:

```bash
sudo systemctl restart lsws
```

---

## 8) Bật SSL Let's Encrypt trong CyberPanel

- Vào **Websites → List Websites → Manage → SSL**
- Chọn **Issue SSL**
- Bật thêm **Force HTTPS** nếu cần.

---

## 9) Checklist sau deploy

```bash
# Backend sống
curl -I http://127.0.0.1:8888

# Frontend domain
curl -I https://yourdomain.com

# API qua domain (đi qua proxy /api)
curl -I https://yourdomain.com/api/health
```

Nếu endpoint `/api/health` chưa có, dùng một endpoint thực tế đang tồn tại trong backend để test.

---

## 10) Quy trình cập nhật phiên bản mới

```bash
cd /home/tinix-bazi
git pull
npm install
npm run build -w frontend
rsync -av --delete /home/tinix-bazi/frontend/dist/ /home/yourdomain.com/public_html/
pm2 restart tinix-bazi-api
```

---

## 11) Lỗi thường gặp

1. **502/503 khi gọi `/api`**
   - Kiểm tra PM2: `pm2 logs tinix-bazi-api`
   - Kiểm tra backend có listen đúng `PORT=8888`.

2. **Refresh route bị 404 (`/profile`, `/admin`...)**
   - Thiếu SPA fallback về `/index.html` trong rewrite.

3. **Frontend gọi sai URL API**
   - Dùng `/api/...` thay vì hard-code `localhost` trong production.

4. **Lỗi CORS**
   - Nếu đã đi cùng domain qua proxy `/api`, thường không cần cấu hình CORS phức tạp.

---

Nếu bạn muốn, mình có thể viết luôn bộ file sẵn dùng cho production:
- `ecosystem.config.js` (PM2)
- `deploy.sh` (1 lệnh build + sync + restart)
- mẫu `vhconf.conf` hoàn chỉnh cho CyberPanel/OpenLiteSpeed.
