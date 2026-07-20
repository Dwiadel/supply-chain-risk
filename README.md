Supply Chain Risk Intelligence Platform

Platform berbasis web untuk memantau risiko rantai pasok (supply chain) suatu negara secara real-time. Dibangun menggunakan Laravel, aplikasi ini membantu pengguna mencari dan membandingkan tingkat risiko antar negara, mulai dari kurs mata uang, sentimen berita, hingga kondisi pelabuhan.

Fitur
Dashboard — ringkasan cepat kondisi risiko rantai pasok, dengan pencarian negara dan shortcut ke negara-negara populer (Germany, Indonesia, China, Australia, Japan, Singapore)
Peta Global — visualisasi risiko rantai pasok dalam bentuk peta dunia
Risk Scoring Engine — mesin penilaian skor risiko untuk mendukung pengambilan keputusan
Data Visualization — grafik dan visualisasi data pendukung analisis
Perbandingan Negara — membandingkan tingkat risiko antar negara
Kurs Mata Uang — pemantauan nilai tukar mata uang
Berita & Sentimen — analisis berita dan sentimen terkait rantai pasok
Pelabuhan — informasi kondisi pelabuhan
Watchlist — menyimpan daftar negara yang ingin dipantau secara berkala
Login Admin — akses khusus admin untuk mengelola data
Teknologi yang Digunakan
Laravel — backend framework (PHP)
Vite — build tool untuk frontend
MySQL — database
Node.js & NPM — untuk kompilasi asset frontend
Instalasi
Clone repository ini
bash
   git clone https://github.com/Dwiadel/supply-chain-risk.git
   cd supply-chain-risk
Install dependency PHP
bash
   composer install
Install dependency frontend
bash
   npm install
Copy file environment dan generate application key
bash
   cp .env.example .env
   php artisan key:generate
Atur koneksi database di file .env, lalu jalankan migrasi
bash
   php artisan migrate
Jalankan aplikasi
bash
   php artisan serve
   npm run dev
Buka http://localhost:8000 di browser
Struktur Proyek

Proyek ini mengikuti struktur standar Laravel:

app/         # logic aplikasi (controller, model, dll)
config/      # konfigurasi aplikasi
database/    # migration dan seeder
public/      # entry point aplikasi
resources/   # view, asset frontend
routes/      # definisi route
storage/     # file storage & log
tests/       # unit & feature test