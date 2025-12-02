# PHP Stream XOR CRUD

Proyek ini adalah aplikasi CRUD sederhana menggunakan PHP Native, PDO, dan enkripsi XOR berbasis stream untuk menyimpan data sensitif di database MySQL.

## Struktur Direktori
```
php-stream-xor-crud-exercise/
├── public/
│   └── index.php
├── src/
│   ├── config.php
│   ├── crypto.php
│   └── db.php
├── dump.sql
└── README.md
```

## Instalasi
1. **Clone repo ini** ke folder htdocs XAMPP Anda.
2. **Buat database** MySQL dan import `dump.sql` untuk membuat tabel `users`.
3. **Edit file `src/config.php`** sesuai konfigurasi database Anda (user, password, host, dsb).
4. Jalankan XAMPP dan akses aplikasi melalui browser: `http://localhost/php-stream-xor-crud-exercise/public/`

## Penggunaan
- Form di bagian atas untuk menambah data baru.
- Data yang sudah ada dapat dihapus atau diedit.
- Semua data terenkripsi di database, hanya didekripsi saat ditampilkan di aplikasi.

## Lisensi
Proyek ini bebas digunakan untuk pembelajaran.
