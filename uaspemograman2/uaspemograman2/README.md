# Pengajuan Paspor — Kantor Imigrasi Cabang
UAS Pemrograman Web II — PHP Native + MySQL

## Cara Menjalankan (XAMPP / Laragon)
1. Salin folder `paspor-app` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Buka phpMyAdmin, import file **schema.sql** (ini akan membuat database `db_paspor` beserta 3 tabelnya).
3. Cek `config.php` — sesuaikan `$user` / `$pass` jika MySQL kamu pakai password.
4. Jalankan Apache + MySQL, lalu buka `http://localhost/paspor-app/`.
5. Otomatis diarahkan ke halaman **Daftar**.

## Struktur File
| File | Fungsi |
|---|---|
| `schema.sql` | Struktur database (3 tabel) |
| `config.php` | Koneksi database |
| `header.php` | Header + navbar (dipakai 3 halaman) |
| `style.css` | Styling |
| `daftar.php` | Modul 1: Pendaftaran |
| `daftar_ulang.php` | Modul 2: Daftar Ulang |
| `pengurusan.php` | Modul 3: Pengurusan Paspor |

## Alur & Logika Bisnis

### 1. Daftar
- Input: Nama Pemohon, Tanggal Daftar.
- Sistem otomatis menghitung **Hari, Tanggal Datang, dan Jam** yang harus dipenuhi pemohon.
- Kapasitas: **maksimal 5 orang per hari** (jam kerja 08:00, 09:00, 10:00, 11:00, 13:00).
- Jika tanggal yang dipilih sudah terisi 5 orang, sistem otomatis mencari **hari berikutnya** yang masih tersedia slot.

### 2. Daftar Ulang
- Pilih No. Daftar (otomatis mengambil Nama Pemohon & jadwal aslinya).
- Input Hari Datang & Tgl Datang aktual (saat pemohon datang ke kantor), Keperluan, dan centang berkas (KTP, KK, Ijazah/Akte).
- Sistem membandingkan Hari/Tgl Datang aktual dengan jadwal hasil Modul 1:
  - **Sesuai → Keterangan = OK**, dan mendapat **No. Antrian otomatis** (auto increment).
  - **Tidak sesuai → Keterangan = Tidak**, tanpa No. Antrian.

### 3. Pengurusan
- Menampilkan daftar antrian (Keterangan = OK) yang siap diproses — klik **Proses**.
- Sistem mengecek kelengkapan berkas (KTP, KK, Ijazah/Akte):
  - **Semua ada → Berkas = Lengkap, Status = Diterima, Keterangan = OK, Pembayaran = Rp355.000**.
  - **Ada yang kurang → Berkas = Tidak Lengkap, Status = Ditolak, Keterangan = Kurang Lengkap, Pembayaran = Rp0**.
- **Pendapatan** = total pembayaran dari seluruh pemohon berstatus Diterima.

## Catatan
- Ganti `[nama mahasiswa]` di `header.php` dengan nama kamu sesuai format soal.
- Semua modul sudah mendukung **Tambah, Edit (Ubah), dan Hapus** data (CRUD) ke database MySQL.
