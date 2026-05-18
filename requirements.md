# Project Requirements & Installation Guide

Dokumen ini berisi informasi mengenai spesifikasi sistem, teknologi yang digunakan, dan panduan instalasi untuk menjalankan **Horizon Event Management System**.

## 💻 Tech Stack
Aplikasi ini dibangun menggunakan arsitektur modern (TALL-stack variant):
- **Framework Backend:** Laravel (v11.x / v13.x)
- **Bahasa Pemrograman:** PHP ^8.3
- **Database:** MySQL 8.0
- **Frontend / UI:** 
  - Blade Templates
  - Tailwind CSS v4 (via Vite)
  - Alpine.js (untuk reaktivitas UI)
- **Real-time Engine:** Laravel Reverb (WebSockets)
- **Development Environment:** Docker (Opsional tapi direkomendasikan)

---

## ⚙️ System Requirements (Tanpa Docker)
Jika Anda ingin menjalankan aplikasi secara langsung di OS Anda (Windows/Mac/Linux) tanpa Docker, pastikan sistem Anda telah terinstal perangkat lunak berikut:

1. **PHP >= 8.3**
   *Ekstensi PHP yang wajib aktif:*
   - `bcmath`
   - `ctype`
   - `fileinfo`
   - `json`
   - `mbstring`
   - `openssl`
   - `pdo_mysql`
   - `tokenizer`
   - `xml`
   - `curl`
   - `gd` atau `imagick` (untuk pemrosesan gambar)
2. **Composer** (v2.x)
3. **Node.js** (>= v18.x) dan **npm** (untuk build frontend assets)
4. **MySQL** Server (>= v8.0) atau MariaDB.

---

## 🚀 Panduan Instalasi (Development)

Terdapat dua cara untuk menjalankan aplikasi ini: **Menggunakan Docker** (sangat mudah) atau **Manual** (Composer & NPM).

### Opsi A: Menggunakan Docker (Sangat Direkomendasikan)
Aplikasi ini sudah dilengkapi dengan konfigurasi `docker-compose.yml` untuk Nginx, PHP, dan MySQL.

1. Clone repositori ini.
2. Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
3. Sesuaikan konfigurasi DB di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=tms_db
   DB_USERNAME=root
   DB_PASSWORD=admin123
   ```
4. Jalankan kontainer Docker di background:
   ```bash
   docker-compose up -d --build
   ```
5. Masuk ke dalam kontainer PHP/App:
   ```bash
   docker-compose exec app sh
   ```
6. Di dalam kontainer, jalankan setup Laravel:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```
7. Aplikasi dapat diakses melalui `http://localhost:8000`

### Opsi B: Instalasi Manual (Localhost / XAMPP / Laragon)

1. Clone repositori ini.
2. Masuk ke direktori project dan install dependensi PHP:
   ```bash
   composer install
   ```
3. Salin konfigurasi environment:
   ```bash
   cp .env.example .env
   ```
4. Buka file `.env` dan atur koneksi database Anda (sesuaikan dengan DB lokal Anda):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Jalankan migrasi database (beserta data dummy awal jika ada):
   ```bash
   php artisan migrate --seed
   ```
7. Buat link untuk storage (agar gambar/file bisa diakses):
   ```bash
   php artisan storage:link
   ```
8. Install dependensi Node.js dan build asset frontend (Tailwind/Alpine):
   ```bash
   npm install
   npm run build
   ```

---

## 📡 Menjalankan Aplikasi (Local Server)

Untuk menjalankan aplikasi secara penuh beserta fitur notifikasi *real-time*, Anda membutuhkan **tiga (3) terminal** yang berjalan bersamaan:

**Terminal 1: Menjalankan Server Web Laravel**
```bash
php artisan serve
```
*(Aplikasi bisa diakses di http://127.0.0.1:8000)*

**Terminal 2: Menjalankan Vite (Frontend Hot-Reload) - Opsional saat dev**
```bash
npm run dev
```

**Terminal 3: Menjalankan Server WebSockets (Laravel Reverb)**
Agar fitur notifikasi bel otomatis bertambah (tanpa *refresh*), Reverb harus menyala:
```bash
php artisan reverb:start
```

---

## 🔑 Catatan Penting
- **Real-time Notifications:** Sistem ini telah diatur untuk tidak menggunakan *polling AJAX* yang membebani server, melainkan menggunakan `laravel-echo` dan WebSockets. Pastikan `.env` Anda memiliki konfigurasi variabel `REVERB_APP_KEY`, dsb.
- **Cara Uji Coba Real-time:** Anda dapat mengujinya di **satu perangkat yang sama** menggunakan 2 browser berbeda (contoh: Chrome dan Firefox) atau 1 browser biasa dan 1 jendela Incognito. Pastikan kedua browser telah di-refresh untuk memuat otentikasi *Axios* terbaru. Saat satu *user* (misal pembuat acara) menerima *Approval* dari user lain di browser berbeda, ikon bel akan bertambah angkanya seketika tanpa perlu *reload* halaman.
- **Queue / Background Jobs:** Jika Anda mengaktifkan pengiriman email atau sinkronisasi asinkron lainnya, pastikan Anda juga menjalankan `php artisan queue:work` di terminal ke-empat.
