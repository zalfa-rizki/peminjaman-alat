# Aplikasi Peminjaman Alat

Aplikasi PHP + MySQL untuk mengelola akun, inventaris alat, pengajuan peminjaman, persetujuan petugas, pengembalian, denda, dan riwayat aktivitas.

## Fitur utama

- Login dan registrasi akun peminjam dengan role admin, petugas, dan peminjam.
- CRUD user dan alat serta log aktivitas.
- Peminjam dapat mengajukan peminjaman dan mengajukan pengembalian barang yang sudah disetujui.
- Petugas mengonfirmasi barang yang benar-benar diterima; stok baru bertambah setelah konfirmasi, dan transaksi tidak dapat dikembalikan dua kali.
- Denda keterlambatan dihitung Rp20.000 per hari.
- Riwayat peminjaman lengkap untuk peminjam dan tampilan semua riwayat untuk admin.

## Instalasi baru

1. Aktifkan Apache/PHP dan MySQL.
2. Buat database `db_peminjaman` di MySQL/phpMyAdmin.
3. Import `database/db_peminjaman.sql`.
4. Sesuaikan host, username, dan password database di `config/database.php` bila diperlukan. Secara default aplikasi mencoba database lama bernama `peminjaman`, lalu `db_peminjaman`, dan memilih database yang memiliki tabel aplikasi; aplikasi juga mencoba password root kosong bila konfigurasi password awal tidak cocok.
5. Buka folder aplikasi melalui web server.

## Memperbarui instalasi yang sudah punya data

Jangan import ulang `db_peminjaman.sql` ke database lama karena file tersebut adalah dump untuk instalasi baru. Pilih database aplikasi yang sekarang digunakan di phpMyAdmin, kemudian import `database/migrasi_pengembalian.sql`. Migrasi ini hanya menormalisasi status kosong menjadi `pending` dan menambahkan status `menunggu_pengembalian` serta `ditolak`; tabel dan data transaksi tidak dihapus.

## Alur pengembalian

1. Peminjam membuka dashboard dan menekan **Ajukan Pengembalian** pada peminjaman berstatus **Disetujui**.
2. Status menjadi **Menunggu konfirmasi pengembalian**. Stok belum bertambah pada tahap ini.
3. Petugas memeriksa barang secara fisik lalu menekan **Terima Barang** di bagian Permintaan Pengembalian.
4. Sistem mengubah status menjadi **Selesai**, menghitung denda bila terlambat, mencatat log, dan menambah stok satu kali.

Aplikasi juga mempertahankan aksi konfirmasi langsung pada daftar Peminjaman Aktif untuk alur kerja petugas yang sudah digunakan sebelumnya.
