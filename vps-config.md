# Panduan Konfigurasi VPS (Laravel 13 + PHP 8.3)

Dokumen ini berisi panduan teknis untuk menyiapkan Virtual Private Server (VPS) agar memiliki lingkungan yang sama persis dengan lingkungan development proyek **Horizon Event Management**.

## 1. Spesifikasi Sistem Operasi
*   **Rekomendasi:** Ubuntu 22.04 LTS atau Ubuntu 24.04 LTS.
*   **User:** Disarankan menggunakan user non-root dengan akses `sudo`.

## 2. Daftar Software yang Harus Diinstal

### A. Web Server & PHP Stack
Instalasi Nginx dan PHP 8.3 beserta ekstensi yang diperlukan oleh Laravel 13.

```bash
# Update repository
sudo apt update && sudo apt upgrade -y

# Tambahkan repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Instal Nginx & PHP 8.3
sudo apt install nginx -y
sudo apt install php8.3-fpm php8.3-mysql php8.3-sqlite3 php8.3-mbstring \
php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath -y
```

### B. Database
Gunakan MySQL untuk skala production yang lebih stabil.

```bash
sudo apt install mysql-server -y
# Lakukan pengamanan database
sudo mysql_secure_installation
```

### C. Runtime & Package Manager
Diperlukan untuk mengelola dependensi backend dan frontend.

```bash
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20.x (LTS) & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### D. Process Manager (Supervisor)
Digunakan untuk menjaga agar Queue Worker tetap berjalan.

```bash
sudo apt install supervisor -y
```

---

## 3. Konfigurasi Nginx
Buat file konfigurasi di `/etc/nginx/sites-available/horizonevent`

```nginx
server {
    listen 80;
    server_name domain_anda.com;
    root /var/www/horizonevent/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Konfigurasi Supervisor (Queue Worker)
Karena proyek menggunakan `QUEUE_CONNECTION=database`, buat file `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/horizonevent/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/horizonevent/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 5. Langkah Post-Deployment
Setelah mengunggah code ke `/var/www/horizonevent`, jalankan perintah berikut:

1.  **Instal Dependensi:**
    ```bash
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    ```
2.  **Atur Izin Folder:**
    ```bash
    sudo chown -R www-data:www-data /var/www/horizonevent
    sudo find /var/www/horizonevent -type f -exec chmod 644 {} \;
    sudo find /var/www/horizonevent -type d -exec chmod 755 {} \;
    sudo chmod -R 775 /var/www/horizonevent/storage
    sudo chmod -R 775 /var/www/horizonevent/bootstrap/cache
    ```
3.  **Environment & Database:**
    *   Salin `.env.example` ke `.env`.
    *   Sesuaikan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
    *   Set `APP_ENV=production` dan `APP_DEBUG=false`.
    *   Jalankan `php artisan migrate --force`.
    *   Jalankan `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
